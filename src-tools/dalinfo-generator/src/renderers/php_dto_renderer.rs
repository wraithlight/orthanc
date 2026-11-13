use std::collections::HashMap;
use crate::models::artifact::Artifact;
use crate::models::dto_definition::{Node, OperationAst, Property};
use crate::renderers::dto_renderer::DTORenderer;
use crate::services::casing_service::CasingService;

pub struct PhpDTORenderer;

struct ClassDefinition {
  name: String,
  properties: Vec<ClassProperty>,
}

struct ClassProperty {
  name: String,
  php_type: String,
  doc_type: Option<String>,
}

struct EnumDefinition {
  name: String,
  values: Vec<String>,
}

struct DictionaryClassDefinition {
  name: String,
  value_doc: String,
}

struct EmitContext {
  classes: Vec<ClassDefinition>,
  enums: Vec<EnumDefinition>,
  dictionary_classes: Vec<DictionaryClassDefinition>,
  type_name_counts: HashMap<String, usize>,
}

impl DTORenderer for PhpDTORenderer {
  fn render(&self, operations: &[OperationAst]) -> Vec<Artifact> {
    let mut artifacts = Vec::new();

    for op in operations {
      let file_name = format!("{}.model.php", op.file_base);

      if let Some(node) = &op.request_node {
        artifacts.push(Artifact {
          path: format!("dtos/request/{}", file_name),
          content: Self::emit_operation(node, "Request", &op.name),
        });
      }

      if let Some(node) = &op.response_node {
        artifacts.push(Artifact {
          path: format!("dtos/response/{}", file_name),
          content: Self::emit_operation(node, "Response", &op.name),
        });
      }
    }

    artifacts
  }
}

impl PhpDTORenderer {
  fn emit_operation(operation_node: &Node, dto_type: &str, operation_name: &str) -> String {
    let root_name = format!(
      "{}{}",
      CasingService::sanitize_identifier(&CasingService::to_pascal_case(operation_name)),
      dto_type,
    );
    let mut context = EmitContext::new();

    let payload_type_name = format!("{}Payload", root_name);
    let (main_node, payload_node) = Self::extract_payload_node(operation_node, &payload_type_name);

    match &payload_node {
      Some(node @ Node::Object { .. }) => {
        context.register_class(&payload_type_name, node);
      }
      Some(Node::Dictionary(value)) => {
        context.register_dictionary_class(&payload_type_name, value);
      }
      _ => {}
    }

    context.register_class(&root_name, &main_node);

    let mut out = String::from("<?php\n");

    for enum_definition in &context.enums {
      out.push_str(&Self::emit_enum(enum_definition));
    }

    for dictionary_class in &context.dictionary_classes {
      out.push_str(&Self::emit_dictionary_class(dictionary_class));
    }

    // nested classes are registered before the root that references them
    for class in &context.classes {
      out.push_str(&Self::emit_class(class));
    }
    out
  }

  /// extracts an object or dictionary "payload" property into a dedicated named type,
  /// mirroring the TypeScript renderer
  fn extract_payload_node(
    operation_node: &Node,
    payload_type_name: &str,
  ) -> (Node, Option<Node>) {
    if let Node::Object { properties } = operation_node {
      if let Some(payload_prop) = properties.iter().find(|p| p.name == "payload") {
        if matches!(&payload_prop.node, Node::Object { .. } | Node::Dictionary(_)) {
          let payload_node = payload_prop.node.clone();
          let main_properties = properties
            .iter()
            .map(|p| {
              if p.name == "payload" {
                Property {
                  name: p.name.clone(),
                  required: p.required,
                  node: Node::TypeRef(payload_type_name.to_string()),
                }
              } else {
                p.clone()
              }
            })
            .collect();
          return (
            Node::Object { properties: main_properties },
            Some(payload_node),
          );
        }
      }
    }
    (operation_node.clone(), None)
  }

  fn emit_enum(enum_definition: &EnumDefinition) -> String {
    let mut out = format!("\nenum {}: string {{\n", enum_definition.name);

    for (index, value) in enum_definition.values.iter().enumerate() {
      out.push_str(&format!("  case {} = \"{}\";\n", Self::case_name(value, index), value));
    }

    out.push_str("}\n");
    out
  }

  /// values without alphanumerics (e.g. "∞") cannot be turned into a case name
  fn case_name(value: &str, index: usize) -> String {
    let pascal: String = CasingService::to_pascal_case(value)
      .chars()
      .filter(|c| c.is_ascii_alphanumeric())
      .collect();

    if pascal.is_empty() || pascal.chars().next().map(|c| c.is_ascii_digit()).unwrap_or(false) {
      return format!("Value{}", index + 1);
    }

    pascal
  }

  /// dictionaries have dynamic keys, so the class wraps ArrayObject and
  /// serializes as a JSON object (a plain PHP array would encode an empty
  /// dictionary as [] instead of {})
  fn emit_dictionary_class(dictionary_class: &DictionaryClassDefinition) -> String {
    format!(
      "\n/** @extends ArrayObject<string, {}> */\nclass {} extends ArrayObject implements JsonSerializable {{\n  public function jsonSerialize(): object {{\n    return (object) $this->getArrayCopy();\n  }}\n}}\n",
      dictionary_class.value_doc,
      dictionary_class.name,
    )
  }

  fn emit_class(class: &ClassDefinition) -> String {
    let mut out = format!("\nclass {} {{\n", class.name);

    for property in &class.properties {
      if let Some(doc_type) = &property.doc_type {
        out.push_str(&format!("  /** @var {} */\n", doc_type));
      }
      out.push_str(&format!("  public {} ${};\n", property.php_type, property.name));
    }

    out.push_str("}\n");
    out
  }
}

impl EmitContext {
  fn new() -> Self {
    Self {
      classes: vec![],
      enums: vec![],
      dictionary_classes: vec![],
      type_name_counts: HashMap::new(),
    }
  }

  fn register_class(&mut self, preferred_name: &str, node: &Node) -> String {
    let name = self.unique_type_name(preferred_name);
    let properties = match node {
      Node::Object { properties } => {
        let mut sorted = properties.clone();
        sorted.sort_by(|a, b| a.name.cmp(&b.name));
        sorted
      }
      // non-object bodies still need a carrier class
      _ => vec![],
    };

    let mut class_properties = vec![];

    for property in properties {
      let child_name = format!(
        "{}{}",
        name,
        CasingService::sanitize_identifier(&CasingService::to_pascal_case(&property.name)),
      );
      let (php_type, doc_type) = self.emit_type(&property.node, &child_name);

      class_properties.push(ClassProperty {
        name: property.name.clone(),
        php_type: Self::nullable(&php_type, property.required),
        doc_type: doc_type.map(|doc| Self::nullable_doc(&doc, property.required)),
      });
    }

    self.classes.push(ClassDefinition { name: name.clone(), properties: class_properties });
    name
  }

  fn register_dictionary_class(&mut self, preferred_name: &str, value: &Node) -> String {
    let name = self.unique_type_name(preferred_name);
    let (value_type, value_doc) = self.emit_type(value, &format!("{}Value", name));
    let value_doc = value_doc.unwrap_or(value_type);
    self.dictionary_classes.push(DictionaryClassDefinition { name: name.clone(), value_doc });
    name
  }

  fn register_enum(&mut self, preferred_name: &str, values: &[String]) -> String {
    let name = self.unique_type_name(preferred_name);
    self.enums.push(EnumDefinition { name: name.clone(), values: values.to_vec() });
    name
  }

  fn emit_type(&mut self, node: &Node, preferred_name: &str) -> (String, Option<String>) {
    match node {
      Node::String => ("string".to_string(), None),
      Node::StringLiteralUnion(values) => (self.register_enum(preferred_name, values), None),
      Node::Number => ("int|float".to_string(), None),
      Node::Boolean => ("bool".to_string(), None),
      Node::TypeRef(name) => (name.clone(), None),
      Node::Object { .. } => (self.register_class(preferred_name, node), None),
      Node::Array(inner) => {
        let (item_type, item_doc) = self.emit_type(inner, &format!("{}Item", preferred_name));
        let doc = item_doc.unwrap_or(item_type);
        ("array".to_string(), Some(format!("list<{}>", doc)))
      }
      Node::Dictionary(value) => {
        let (value_type, value_doc) = self.emit_type(value, &format!("{}Value", preferred_name));
        let doc = value_doc.unwrap_or(value_type);
        ("array".to_string(), Some(format!("array<string, {}>", doc)))
      }
      Node::Union(variants) => {
        let mut parts: Vec<String> = vec![];
        for (index, variant) in variants.iter().enumerate() {
          let (part, _) = self.emit_type(variant, &format!("{}Variant{}", preferred_name, index + 1));
          // PHP rejects duplicate types in a union
          if !parts.contains(&part) {
            parts.push(part);
          }
        }
        (parts.join("|"), None)
      }
    }
  }

  fn unique_type_name(&mut self, preferred_name: &str) -> String {
    let count = self.type_name_counts.entry(preferred_name.to_string()).or_insert(0);
    *count += 1;

    if *count == 1 {
      preferred_name.to_string()
    } else {
      format!("{}{}", preferred_name, count)
    }
  }

  fn nullable(php_type: &str, required: bool) -> String {
    if required {
      return php_type.to_string();
    }

    if php_type.contains('|') {
      format!("{}|null", php_type)
    } else {
      format!("?{}", php_type)
    }
  }

  fn nullable_doc(doc_type: &str, required: bool) -> String {
    if required {
      doc_type.to_string()
    } else {
      format!("{}|null", doc_type)
    }
  }
}
