use crate::models::artifact::Artifact;
use crate::models::enum_definition::{EnumDefinition, EnumKind};
use crate::renderers::enum_renderer::EnumRenderer;
use crate::services::casing_service::CasingService;

pub struct PhpEnumRenderer;

impl EnumRenderer for PhpEnumRenderer {
  fn render(&self, definitions: &[EnumDefinition]) -> Vec<Artifact> {
    definitions
      .iter()
      .map(Self::render_definition)
      .collect()
  }
}

impl PhpEnumRenderer {
  fn render_definition(definition: &EnumDefinition) -> Artifact {
    let mut content = String::from("<?php\n\n");
    content.push_str(&format!("enum {}: string {{\n", definition.type_name));

    for member in &definition.members {
      content.push_str(&format!(
        "  case {} = \"{}\";\n",
        CasingService::to_pascal_case(&member.value),
        member.value,
      ));
    }

    content.push_str("}\n");

    Artifact {
      path: format!("{}/{}.enum.php", Self::directory(definition.kind), definition.file_base),
      content,
    }
  }

  fn directory(kind: EnumKind) -> &'static str {
    match kind {
      EnumKind::HeaderName => "headers/names",
      EnumKind::HeaderValue => "headers/values",
    }
  }
}
