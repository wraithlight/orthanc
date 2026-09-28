use crate::models::artifact::Artifact;
use crate::models::enum_definition::{EnumDefinition, EnumKind, EnumMember};
use crate::renderers::enum_renderer::EnumRenderer;
use crate::services::casing_service::CasingService;

pub struct TypeScriptEnumRenderer;

impl EnumRenderer for TypeScriptEnumRenderer {
  fn render(&self, definitions: &[EnumDefinition]) -> Vec<Artifact> {
    let mut artifacts: Vec<Artifact> = definitions
      .iter()
      .map(Self::render_definition)
      .collect();

    artifacts.extend(Self::render_barrels(definitions));
    artifacts
  }
}

impl TypeScriptEnumRenderer {
  fn render_definition(definition: &EnumDefinition) -> Artifact {
    let mut content = String::new();
    let keyword = match definition.kind {
      EnumKind::HeaderName => "export const enum",
      EnumKind::HeaderValue => "export enum",
    };

    content.push_str(&format!("{} {} {{\n", keyword, definition.type_name));

    for member in &definition.members {
      content.push_str(&format!(
        "  {} = \"{}\",\n",
        Self::member_key(definition.kind, member),
        member.value,
      ));
    }

    content.push_str("}\n");

    Artifact {
      path: format!("{}/{}.enum.ts", Self::directory(definition.kind), definition.file_base),
      content,
    }
  }

  fn member_key(kind: EnumKind, member: &EnumMember) -> String {
    match kind {
      EnumKind::HeaderName => CasingService::to_screaming_snake_case(&member.source_name),
      EnumKind::HeaderValue => CasingService::to_pascal_case(&member.source_name),
    }
  }

  fn directory(kind: EnumKind) -> &'static str {
    match kind {
      EnumKind::HeaderName => "headers/names",
      EnumKind::HeaderValue => "headers/values",
    }
  }

  fn render_barrels(definitions: &[EnumDefinition]) -> Vec<Artifact> {
    vec![
      Artifact {
        path: "headers/index.ts".into(),
        content: "export * from \"./names\";\nexport * from \"./values\";".into(),
      },
      Artifact {
        path: "headers/names/index.ts".into(),
        content: Self::render_exports(definitions, EnumKind::HeaderName),
      },
      Artifact {
        path: "headers/values/index.ts".into(),
        content: Self::render_exports(definitions, EnumKind::HeaderValue),
      },
    ]
  }

  fn render_exports(definitions: &[EnumDefinition], kind: EnumKind) -> String {
    definitions
      .iter()
      .filter(|definition| definition.kind == kind)
      .map(|definition| format!("export * from \"./{}.enum\";", definition.file_base))
      .collect::<Vec<_>>()
      .join("\n")
  }
}
