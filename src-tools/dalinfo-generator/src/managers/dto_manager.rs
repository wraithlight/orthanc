use serde_json::Value;
use std::collections::{HashMap, HashSet};
use crate::models::artifact::Artifact;

pub struct DTOManager;

pub struct SchemaRegistry {
  schemas: HashMap<String, Value>,
}

pub struct OperationDefinition {
  pub operation_id: String,
  pub request: Option<Value>,
  pub response: Option<Value>,
}

pub struct OperationAst {
  pub name: String,
  pub request_node: Option<Node>,
  pub response_node: Option<Node>,
}

#[derive(Clone)]
pub enum Node {
  String,
  Number,
  Boolean,
  Array(Box<Node>),
  Union(Vec<Node>),
  StringLiteralUnion(Vec<String>),
  Object {
    properties: Vec<Property>,
  },
}

#[derive(Clone)]
pub struct Property {
  pub name: String,
  pub required: bool,
  pub node: Node,
}

impl DTOManager {
  pub fn create_dtos(
    doc: &Value,
    _target: &str,
  ) -> anyhow::Result<Vec<Artifact>> {
    let registry = Self::build_registry(doc);
    let operations = Self::collect_operations(doc)?;
    let mut result = vec![];
    for op in operations {
      let ast = Self::build_operation_ast(&op, &registry)?;
      result.extend(Self::generate_typescript(vec![ast])?);
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
  fn generate_typescript(
    operations: Vec<OperationAst>,
  ) -> anyhow::Result<Vec<Artifact>> {
    let mut out = Vec::new();

    for op in operations {
      let file_name = Self::build_file_name(&op.name);
      let content = Self::emit_operation(&op);
      out.push(Artifact {
        path: file_name,
        content,
      });
    }
    Ok(out)
  }

  fn build_file_name(operation_id: &str) -> String {
    let normalized = Self::to_kebab_case(operation_id);
    format!("{}.model.ts", normalized)
  }

  fn to_kebab_case(input: &str) -> String {
    let mut out = String::new();
    for (i, c) in input.chars().enumerate() {
      if c.is_uppercase() {
        if i > 0 {
          out.push('-');
        }

        for lower in c.to_lowercase() {
          out.push(lower);
        }
      } else {
        out.push(c);
      }
    }
    out
  }

fn emit_operation(operation: &OperationAst) -> String {
    let mut out = String::new();

    if let Some(req) = &operation.request_node {
        let req_ts = Self::emit_node(req);
        out.push_str(&format!(
            "export interface {}Request {}\n\n",
            Self::str_capitalize_first(&operation.name),
            req_ts
        ));
    // } else {
    //     out.push_str(&format!(
    //         "export interface {}Request {{}}\n\n",
    //         operation.name
    //     ));
    }

    if let Some(res) = &operation.response_node {
        let res_ts = Self::emit_node(res);
        out.push_str(&format!(
            "export interface {}Response {}\n",
            Self::str_capitalize_first(&operation.name),
            res_ts
        ));
    // } else {
    //     out.push_str(&format!(
    //         "export interface {}Response {{}}\n",
    //         operation.name
    //     ));
    }

    out
}

fn str_capitalize_first(s: &str) -> String {
  format!("{}{}", s.chars().next().unwrap().to_uppercase(), 
  s.chars().skip(1).collect::<String>())
}

fn emit_node(node: &Node) -> String {
    match node {
        Node::String => "string".to_string(),
        Node::Number => "number".to_string(),
        Node::Boolean => "boolean".to_string(),
        Node::StringLiteralUnion(values) => {
            values
                .iter()
                .map(|v| format!("\"{}\"", v))
                .collect::<Vec<_>>()
                .join(" | ")
        }
        Node::Array(inner) => {
            format!("{}[]", Self::emit_node(inner))
        }
        Node::Union(variants) => {
            variants
                .iter()
                .map(|v| Self::emit_node(v))
                .collect::<Vec<_>>()
                .join(" | ")
        }
        Node::Object { properties, .. } => {
            let mut props = properties.clone();
            props.sort_by(|a, b| a.name.cmp(&b.name));

            let mut out = String::from("{ ");

            for prop in props {
                let ts_type = Self::emit_node(&prop.node);

                if prop.required {
                    out.push_str(&format!("{}: {}; ", prop.name, ts_type));
                } else {
                    out.push_str(&format!("{}?: {}; ", prop.name, ts_type));
                }
            }

            out.push_str("}");
            out
        }
    }
}

  // fn emit_object(
  //   props: &[Property]
  // ) -> String {
  //   let mut out = String::from("{\n");
  //   for prop in props {
  //     let ts_type = Self::emit_node(
  //       &prop.node,
  //     );
  //     let optional = if prop.required { "" } else { "?" };
  //     out.push_str(&format!(
  //       "{}{}: {};\n",
  //       prop.name,
  //       optional,
  //       ts_type
  //     ));
  //   }
  //   out
  // }

}

impl SchemaRegistry {
  pub fn get(&self, name: &str) -> Option<&Value> {
    self.schemas.get(name)
  }
}