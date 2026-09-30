use crate::models::artifact::Artifact;
use crate::models::path_definition::{PathDefinition, PathSegment};
use crate::renderers::path_renderer::PathRenderer;
use crate::services::casing_service::CasingService;

pub struct PhpPathRenderer;

impl PathRenderer for PhpPathRenderer {
  fn render(&self, definitions: &[PathDefinition]) -> Vec<Artifact> {
    definitions
      .iter()
      .map(Self::render_definition)
      .collect()
  }
}

impl PhpPathRenderer {
  fn render_definition(definition: &PathDefinition) -> Artifact {
    let mut content = format!(
      "<?php\n\nconst {} = \"{}\";\n",
      Self::const_name(&definition.operation_id),
      Self::render_path(definition),
    );

    for param in &definition.query_params {
      content.push_str(&format!(
        "const {} = \"{}\";\n",
        Self::query_const_name(&definition.operation_id, &param.name),
        param.name,
      ));
    }

    Artifact {
      path: format!("paths/{}.path.const.php", definition.file_base),
      content,
    }
  }

  fn const_name(operation_id: &str) -> String {
    format!("API_{}_PATH", CasingService::camel_to_screaming_snake_case(operation_id))
  }

  fn query_const_name(operation_id: &str, param_name: &str) -> String {
    format!(
      "API_{}_QUERY__{}",
      CasingService::camel_to_screaming_snake_case(operation_id),
      CasingService::to_screaming_snake_case(param_name),
    )
  }

  /// phpapi routes declare path params as `:param`, so no arguments are needed.
  fn render_path(definition: &PathDefinition) -> String {
    definition
      .segments
      .iter()
      .map(|segment| match segment {
        PathSegment::Literal(literal) => literal.clone(),
        PathSegment::Param(name) => format!(":{}", CasingService::to_camel_case(name)),
      })
      .collect()
  }
}
