use crate::models::artifact::Artifact;
use crate::models::path_definition::{PathDefinition, PathParameter, PathSegment};
use crate::renderers::path_renderer::PathRenderer;
use crate::services::casing_service::CasingService;

pub struct TypeScriptPathRenderer;

impl PathRenderer for TypeScriptPathRenderer {
  fn render(&self, definitions: &[PathDefinition]) -> Vec<Artifact> {
    let mut artifacts: Vec<Artifact> = definitions
      .iter()
      .map(Self::render_definition)
      .collect();

    artifacts.push(Self::render_barrel(definitions));
    artifacts
  }
}

impl TypeScriptPathRenderer {
  fn render_definition(definition: &PathDefinition) -> Artifact {
    let const_name = Self::const_name(&definition.operation_id);
    let args = Self::render_args(definition);
    let interpolated_path = Self::render_path(definition);

    let content = if args.is_empty() {
      format!("export const {} = () => `{}` as const;", const_name, interpolated_path)
    } else if definition.query_params.is_empty() {
      format!(
        "export const {} = ({}) => `{}` as const;",
        const_name, args, interpolated_path
      )
    } else {
      Self::render_with_query(&const_name, &args, &interpolated_path, &definition.query_params)
    };

    Artifact {
      path: format!("paths/{}.path.const.ts", definition.file_base),
      content,
    }
  }

  fn const_name(operation_id: &str) -> String {
    format!("API_{}_PATH", CasingService::camel_to_screaming_snake_case(operation_id))
  }

  fn render_args(definition: &PathDefinition) -> String {
    definition
      .path_params
      .iter()
      .chain(definition.query_params.iter())
      .map(|param| {
        if param.required {
          format!("{}: string", param.arg_name)
        } else {
          format!("{}?: string", param.arg_name)
        }
      })
      .collect::<Vec<_>>()
      .join(", ")
  }

  fn render_path(definition: &PathDefinition) -> String {
    definition
      .segments
      .iter()
      .map(|segment| match segment {
        PathSegment::Literal(literal) => literal.clone(),
        PathSegment::Param(name) => format!("${{{}}}", CasingService::to_camel_case(name)),
      })
      .collect()
  }

  fn render_with_query(
    const_name: &str,
    args: &str,
    interpolated_path: &str,
    query_params: &[PathParameter],
  ) -> String {
    let mut body = String::from("  const queryParams = new URLSearchParams();\n");

    for param in query_params {
      let set_call = format!(
        "  queryParams.set(\"{}\", {});\n",
        param.name, param.arg_name
      );

      if param.required {
        body.push_str(&set_call);
      } else {
        body.push_str(&format!(
          "  if ({} !== undefined) {{\n  {}  }}\n",
          param.arg_name, set_call
        ));
      }
    }

    body.push_str(&format!(
      "  return `{}?${{queryParams.toString()}}` as const;\n",
      interpolated_path
    ));

    format!("export const {} = ({}) => {{\n{}}};", const_name, args, body)
  }

  fn render_barrel(definitions: &[PathDefinition]) -> Artifact {
    Artifact {
      path: "paths/index.ts".into(),
      content: definitions
        .iter()
        .map(|definition| format!("export * from \"./{}.path.const\";", definition.file_base))
        .collect::<Vec<_>>()
        .join("\n"),
    }
  }
}
