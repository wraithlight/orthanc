use std::collections::HashMap;
use crate::models::artifact::Artifact;
use crate::models::dto_definition::{Node, OperationAst, Property};
use crate::renderers::dto_renderer::DTORenderer;
use crate::services::casing_service::CasingService;

pub struct TypeScriptDTORenderer;

struct EmitContext {
  base_name: String,
  literal_types: Vec<LiteralType>,
  literal_name_counts: HashMap<String, usize>,
}

struct LiteralType {
  name: String,
  values: Vec<String>,
}

impl DTORenderer for TypeScriptDTORenderer {
  fn render(&self, operations: &[OperationAst]) -> Vec<Artifact> {
    let mut artifacts = Vec::new();
    let mut request_bases = Vec::new();
    let mut response_bases = Vec::new();

    for op in operations {
      let file_name = format!("{}.model.ts", op.file_base);
      let req_content = Self::emit_operation_request(op);
      let res_content = Self::emit_operation_response(op);

      if !req_content.is_empty() {
        request_bases.push(op.file_base.clone());
        artifacts.push(Artifact {
          path: format!("dtos/request/{}", file_name),
          content: req_content,
        });
      }

      if !res_content.is_empty() {
        response_bases.push(op.file_base.clone());
        artifacts.push(Artifact {
          path: format!("dtos/response/{}", file_name),
          content: res_content,
        });
      }
    }

    artifacts.extend(Self::render_barrels(&request_bases, &response_bases));
    artifacts
  }
}

impl TypeScriptDTORenderer {
  fn render_barrels(request_bases: &[String], response_bases: &[String]) -> Vec<Artifact> {
    vec![
      Artifact {
        path: "dtos/index.ts".into(),
        content: "export * from \"./request\";\nexport * from \"./response\";".into(),
      },
      Artifact {
        path: "dtos/request/index.ts".into(),
        content: Self::render_exports(request_bases),
      },
      Artifact {
        path: "dtos/response/index.ts".into(),
        content: Self::render_exports(response_bases),
      },
    ]
  }

  fn render_exports(file_bases: &[String]) -> String {
    file_bases
      .iter()
      .map(|base| format!("export * from \"./{}.model\";", base))
      .collect::<Vec<_>>()
      .join("\n")
  }

  fn emit_operation_request(operation: &OperationAst) -> String {
    if let Some(req) = &operation.request_node {
      return Self::emit_operation(req, "Request", &operation.name);
    }
    String::new()
  }

  fn emit_operation_response(operation: &OperationAst) -> String {
    if let Some(res) = &operation.response_node {
      return Self::emit_operation(res, "Response", &operation.name);
    }
    String::new()
  }

  fn emit_operation(
    operation_node: &Node,
    dto_type: &str,
    operation_name: &str
  ) -> String {
    let type_base_name = format!(
      "{}{}",
      Self::str_capitalize_first(operation_name),
      dto_type,
    );
    let mut context = EmitContext::new(type_base_name.clone());
    let mut out = String::new();

    let payload_type_name = format!("{}Payload", type_base_name);
    let (main_node, payload_node) = Self::extract_payload_node(operation_node, &payload_type_name);

    let payload_ts = payload_node
      .as_ref()
      .map(|node| Self::emit_node(node, &mut context));

    let req_ts = Self::emit_node(&main_node, &mut context);

    for literal in &context.literal_types {
      out.push_str(&format!(
        "export type {} = {};\n",
        literal.name,
        Self::emit_literal_union(&literal.values),
      ));
    }

    if !context.literal_types.is_empty() {
      out.push('\n');
    }

    if let Some(payload_ts) = payload_ts {
      if matches!(payload_node, Some(Node::Dictionary(_))) {
        out.push_str(&format!("export type {} = {};\n\n", payload_type_name, payload_ts));
      } else {
        out.push_str(&format!(
          "export interface {} {}\n\n",
          payload_type_name,
          payload_ts,
        ));
      }
    }

    out.push_str(&format!(
      "export interface {}{} {}",
      Self::str_capitalize_first(&operation_name),
      dto_type,
      req_ts
    ));

    out
  }

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

  fn str_capitalize_first(s: &str) -> String {
    format!("{}{}", s.chars().next().unwrap().to_uppercase(),
    s.chars().skip(1).collect::<String>())
  }

  fn emit_node(node: &Node, context: &mut EmitContext) -> String {
    Self::emit_node_with_indent(node, 0, context, "")
  }

  fn emit_node_with_indent(
    node: &Node,
    depth: usize,
    context: &mut EmitContext,
    property_path: &str,
  ) -> String {
    match node {
        Node::String => "string".to_string(),
        Node::Number => "number".to_string(),
        Node::Boolean => "boolean".to_string(),
        Node::StringLiteralUnion(values) => context.register_string_literal_union(values, property_path),
        Node::TypeRef(name) => name.clone(),
        Node::Dictionary(value) => format!(
          "Record<string, {}>",
          Self::emit_node_with_indent(value, depth, context, property_path),
        ),
        Node::Array(inner) => {
          format!("{}[]", Self::emit_node_with_indent(inner, depth, context, property_path))
        }
        Node::Union(variants) => {
            variants
                .iter()
            .map(|v| Self::emit_node_with_indent(v, depth, context, property_path))
                .collect::<Vec<_>>()
                .join(" | ")
        }
        Node::Object { properties, .. } => {
            let mut props = properties.clone();
            props.sort_by(|a, b| a.name.cmp(&b.name));

          if props.is_empty() {
            return "{}".to_string();
          }

          let mut out = String::from("{\n");

            for prop in props {
            let child_path = format!(
              "{}{}",
              property_path,
              Self::str_capitalize_first(&prop.name),
            );
            let ts_type = Self::emit_node_with_indent(
              &prop.node,
              depth + 1,
              context,
              &child_path,
            );
            out.push_str(&Self::indent(depth + 1));

                if prop.required {
              out.push_str(&format!("{}: {};\n", prop.name, ts_type));
                } else {
              out.push_str(&format!("{}?: {};\n", prop.name, ts_type));
                }
            }

          out.push_str(&Self::indent(depth));
          out.push_str("}");
            out
        }
    }
  }

  fn emit_literal_union(values: &[String]) -> String {
    values
      .iter()
      .map(|v| format!("\"{}\"", Self::escape_typescript_string(v)))
      .collect::<Vec<_>>()
      .join(" | ")
  }

  fn escape_typescript_string(value: &str) -> String {
    value.replace('\\', "\\\\").replace('"', "\\\"")
  }

  fn indent(depth: usize) -> String {
    " ".repeat(depth * 2)
  }
}

impl EmitContext {
  fn new(base_name: String) -> Self {
    Self {
      base_name,
      literal_types: vec![],
      literal_name_counts: HashMap::new(),
    }
  }

  fn register_string_literal_union(&mut self, values: &[String], property_path: &str) -> String {
    let base_name = format!(
      "{}{}",
      CasingService::sanitize_identifier(&self.base_name),
      CasingService::sanitize_identifier(property_path),
    );
    let count = self.literal_name_counts.entry(base_name.clone()).or_insert(0);
    *count += 1;
    let name = if *count == 1 {
      base_name
    } else {
      format!("{}{}", base_name, count)
    };

    self.literal_types.push(LiteralType {
      name: name.clone(),
      values: values.to_vec(),
    });

    name
  }
}
