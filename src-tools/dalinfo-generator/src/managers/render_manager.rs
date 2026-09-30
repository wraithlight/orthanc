use crate::models::artifact::Artifact;
use crate::models::enum_definition::EnumDefinition;
use crate::models::language::Language;
use crate::models::path_definition::PathDefinition;
use crate::renderers::enum_renderer::EnumRenderer;
use crate::renderers::path_renderer::PathRenderer;
use crate::renderers::php_enum_renderer::PhpEnumRenderer;
use crate::renderers::php_path_renderer::PhpPathRenderer;
use crate::renderers::typescript_enum_renderer::TypeScriptEnumRenderer;
use crate::renderers::typescript_path_renderer::TypeScriptPathRenderer;

pub struct RenderManager;

impl RenderManager {
  pub fn render_enums_sync(
    definitions: &[EnumDefinition],
    language: Language,
  ) -> Result<Vec<Artifact>, String> {
    Ok(Self::enum_renderer_for(language).render(definitions))
  }

  pub fn render_paths_sync(
    definitions: &[PathDefinition],
    language: Language,
  ) -> Result<Vec<Artifact>, String> {
    Ok(Self::path_renderer_for(language).render(definitions))
  }

  fn enum_renderer_for(language: Language) -> Box<dyn EnumRenderer> {
    match language {
      Language::TypeScript => Box::new(TypeScriptEnumRenderer),
      Language::Php => Box::new(PhpEnumRenderer),
    }
  }

  fn path_renderer_for(language: Language) -> Box<dyn PathRenderer> {
    match language {
      Language::TypeScript => Box::new(TypeScriptPathRenderer),
      Language::Php => Box::new(PhpPathRenderer),
    }
  }
}
