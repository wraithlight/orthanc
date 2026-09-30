use serde_json::Value;
use std::collections::{HashMap, HashSet};
use crate::models::dto_definition::{Node, OperationAst, Property};
use crate::services::casing_service::CasingService;

pub struct DTOManager;

pub struct SchemaRegistry {
  schemas: HashMap<String, Value>,
}

pub struct OperationDefinition {
  pub operation_id: String,
  pub request: Option<Value>,
  pub response: Option<Value>,
}

impl DTOManager {
  /// Emits a language-agnostic AST; rendering is delegated to a per-language renderer.
  pub fn generate_dtos_sync(doc: &Value) -> anyhow::Result<Vec<OperationAst>> {
    let registry = Self::build_registry(doc);
    let operations = Self::collect_operations(doc)?;
    let mut result = vec![];
    for op in operations {
      result.push(Self::build_operation_ast(&op, &registry)?);
    }
    Ok(result)
  }

  fn build_registry(doc: &Value) -> SchemaRegistry {
    let mut schemas = HashMap::new();
    if let Some(obj) = doc
      .get("components")
      .and_then(|c| c.get("schemas"))
      .and_then(|s| s.as_object())
    {
      for (name, schema) in obj {
        schemas.insert(name.clone(), schema.clone());
      }
    }
    SchemaRegistry { schemas }
  }

  fn extract_ref_name(r: &str) -> String {
    r.rsplit('/')
      .next()
      .unwrap_or(r)
      .to_string()
  }

  fn collect_operations(doc: &Value) -> anyhow::Result<Vec<OperationDefinition>> {
    let mut ops = vec![];
    let paths = doc
      .get("paths")
      .and_then(|v| v.as_object())
      .ok_or_else(|| anyhow::anyhow!("Missing paths"))?;

    for (_path, methods) in paths {
      let methods_obj = match methods.as_object() {
        Some(m) => m,
        None => continue,
      };

      for (http_method, operation) in methods_obj {
        if !matches!(
          http_method.as_str(),
          "get" | "post" | "put" | "delete" | "patch"
        ) {
          continue;
        }

        let operation_id = operation
          .get("operationId")
          .and_then(|v| v.as_str())
          .unwrap_or(http_method)
          .to_string();

        let request = Self::find_request_schema(operation);
        let response = Self::find_success_response_schema(operation);

        ops.push(OperationDefinition {
          operation_id,
          request,
          response,
          });
      }
    }
    Ok(ops)
  }

  fn find_request_schema(operation: &Value) -> Option<Value> {
    operation
      .get("requestBody")?
      .get("content")?
      .get("application/json")?
      .get("schema")
      .cloned()
  }

  fn find_success_response_schema(operation: &Value) -> Option<Value> {
    operation
      .get("responses")?
      .get("200")?
      .get("content")?
      .get("application/json")?
      .get("schema")
      .cloned()
  }

  fn resolve_schema(
    schema: &Value,
    registry: &SchemaRegistry,
  ) -> anyhow::Result<Value> {

    if schema.get("$ref").is_some() {
      return Self::resolve_ref(schema, registry);
    }

    if let Some(all_of) = schema.get("allOf").and_then(|v| v.as_array()) {
      return Self::resolve_all_of(all_of, registry);
    }

    if let Some(one_of) = schema.get("oneOf").and_then(|v| v.as_array()) {
      return Self::resolve_one_of(one_of, registry);
    }

    if schema.get("type").and_then(|t| t.as_str()) == Some("array") {
      return Self::resolve_array(schema, registry);
    }

    if schema.get("type").and_then(|t| t.as_str()) == Some("object") || schema.get("properties").is_some()
    {
      return Self::resolve_object(schema, registry);
    }

    Ok(schema.clone())
  }

  fn resolve_ref(
    schema: &Value,
    registry: &SchemaRegistry,
  ) -> anyhow::Result<Value> {
    let ref_str = schema
        .get("$ref")
        .and_then(|v| v.as_str())
        .ok_or_else(|| anyhow::anyhow!("Invalid $ref"))?;
    let name = Self::extract_ref_name(ref_str);
    let resolved = registry
        .get(&name)
        .ok_or_else(|| anyhow::anyhow!("Schema not found: {}", name))?;
    Self::resolve_schema(resolved, registry)
  }

  fn resolve_all_of(
    items: &[Value],
    registry: &SchemaRegistry,
  ) -> anyhow::Result<Value> {
    let mut merged: Option<Value> = None;
    for item in items {
      let resolved = Self::resolve_schema(item, registry)?;
      merged = match merged {
        None => Some(resolved),
        Some(existing) => Some(Self::merge_objects(&existing, &resolved)),
      };
    }
    Ok(merged.unwrap_or(Value::Null))
  }

  fn resolve_one_of(
    items: &[Value],
    registry: &SchemaRegistry,
  ) -> anyhow::Result<Value> {
    let mut resolved_items = vec![];
    for item in items {
      let resolved = Self::resolve_schema(item, registry)?;
      resolved_items.push(resolved);
    }

    let mut obj = serde_json::Map::new();
    obj.insert(
        "oneOf".to_string(),
        Value::Array(resolved_items),
    );

    Ok(Value::Object(obj))
  }

  fn resolve_object(
    schema: &Value,
    registry: &SchemaRegistry,
  ) -> anyhow::Result<Value> {
    let mut result = serde_json::Map::new();
    if let Some(props) = schema.get("properties").and_then(|v| v.as_object()) {
      let mut resolved_props = serde_json::Map::new();

      for (name, prop_schema) in props {
        let resolved = Self::resolve_schema(prop_schema, registry)?;
        resolved_props.insert(name.clone(), resolved);
      }

      result.insert(
        "properties".to_string(),
        Value::Object(resolved_props),
      );
    }

    if let Some(required) = schema.get("required") {
      result.insert("required".to_string(), required.clone());
    }

    if let Some(value_schema) = schema.get("additionalProperties") {
      result.insert(
        "additionalProperties".to_string(),
        Self::resolve_schema(value_schema, registry)?,
      );
    }

    result.insert(
      "type".to_string(),
      Value::String("object".to_string()),
    );

    Ok(Value::Object(result))
  }

  fn resolve_array(
    schema: &Value,
    registry: &SchemaRegistry,
  ) -> anyhow::Result<Value> {

    let mut result = serde_json::Map::new();

    result.insert(
      "type".to_string(),
      Value::String("array".to_string()),
    );

    if let Some(items) = schema.get("items") {
      let resolved_items = Self::resolve_schema(items, registry)?;
      result.insert("items".to_string(), resolved_items);
    }

    Ok(Value::Object(result))
  }

  fn merge_objects(left: &Value, right: &Value) -> Value {
    let mut result = left.clone();
    let left_obj = result.as_object_mut();
    let right_obj = right.as_object();

    match (left_obj, right_obj) {
      (Some(l), Some(r)) => {
        for (k, v) in r {
          match l.get_mut(k) {
            Some(existing) => {
              if existing.is_object() && v.is_object() {
                *existing = Self::merge_objects(existing, v);
              } else {
                l.insert(k.clone(), v.clone());
              }
            }
            None => {
              l.insert(k.clone(), v.clone());
            }
          }
        }
      }
      _ => return right.clone(),
    }
    result
  }

  fn collect_required(schema: &Value) -> HashSet<String> {
    let mut set = HashSet::new();
    if let Some(req) = schema.get("required").and_then(|v| v.as_array()) {
      for item in req {
        if let Some(name) = item.as_str() {
          set.insert(name.to_string());
        }
      }
    }
    set
  }

  fn build_operation_ast(
    operation: &OperationDefinition,
    registry: &SchemaRegistry,
  ) -> anyhow::Result<OperationAst> {
    let request_node = match &operation.request {
      Some(req) => Some(Self::build_node(req, registry)?),
      None => None,
    };

    let response_node = match &operation.response {
      Some(res) => Some(Self::build_node(res, registry)?),
      None => None,
    };

    Ok(OperationAst {
      name: operation.operation_id.clone(),
      file_base: CasingService::camel_to_kebab_case(&operation.operation_id),
      request_node,
      response_node,
    })
  }

  fn build_node(schema: &Value, registry: &SchemaRegistry) -> anyhow::Result<Node> {
    let schema = Self::resolve_schema(schema, registry)?;
    if schema.get("oneOf").is_some() {
      return Self::build_union_node(&schema, registry);
    }
    if schema.get("enum").is_some() {
      return Self::build_string_enum_node(&schema);
    }
    if let Some(value_schema) = schema.get("additionalProperties") {
      return Ok(Node::Dictionary(Box::new(Self::build_node(value_schema, registry)?)));
    }

    match schema.get("type").and_then(|v| v.as_str()) {
      Some("string") => Ok(Node::String),
      Some("number") => Ok(Node::Number),
      Some("boolean") => Ok(Node::Boolean),
      Some("array") => Self::build_array_node(&schema, &registry),
      Some("object") => Self::build_object_node(&schema, &registry),
      _ => {
        if schema.get("properties").is_some() {
        return Self::build_object_node(&schema, &registry);
      }
      Ok(Node::String)
      }
    }
  }

  fn build_object_node(schema: &Value, registry: &SchemaRegistry) -> anyhow::Result<Node> {
    let mut properties = vec![];
    let required_set = Self::collect_required(schema);

    if let Some(props) = schema.get("properties").and_then(|v| v.as_object()) {
      for (name, prop_schema) in props {
        let node = Self::build_node(prop_schema, registry)?;
        properties.push(Property { name: name.clone(), node, required: required_set.contains(name) });
      }
    }

    Ok(Node::Object {
      properties,
    })
  }

  fn build_array_node(schema: &Value, registry: &SchemaRegistry) -> anyhow::Result<Node> {
    let items = schema
      .get("items")
      .ok_or_else(|| anyhow::anyhow!("Array missing items"))?;

    let item_node = Self::build_node(items, registry)?;
    Ok(Node::Array(Box::new(item_node)))
  }

  fn build_union_node(
    schema: &Value,
    registry: &SchemaRegistry
  ) -> anyhow::Result<Node> {

    let mut variants = vec![];

    let items = schema
        .get("oneOf")
        .or_else(|| schema.get("anyOf"))
        .and_then(|v| v.as_array())
        .ok_or_else(|| anyhow::anyhow!("Union schema missing oneOf/anyOf"))?;

    for item in items {
        let node = Self::build_node(item, registry)?;
        variants.push(node);
    }

    Ok(Node::Union(variants))
  }

  fn build_string_enum_node(schema: &Value) -> anyhow::Result<Node> {

    let values = schema
        .get("enum")
        .and_then(|v| v.as_array())
        .ok_or_else(|| anyhow::anyhow!("Missing enum field"))?
        .iter()
        .filter_map(|v| v.as_str())
        .map(|s| s.to_string())
        .collect::<Vec<_>>();

    if values.is_empty() {
        return Err(anyhow::anyhow!("Enum is empty or invalid"));
    }

    Ok(Node::StringLiteralUnion(values))
  }
}

impl SchemaRegistry {
  pub fn get(&self, name: &str) -> Option<&Value> {
    self.schemas.get(name)
  }
}
