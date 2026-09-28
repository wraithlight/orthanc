use crate::models::artifact::Artifact;
use crate::models::enum_definition::EnumDefinition;
use crate::models::language::Language;
use crate::renderers::enum_renderer::EnumRenderer;
use crate::renderers::php_enum_renderer::PhpEnumRenderer;
use crate::renderers::typescript_enum_renderer::TypeScriptEnumRenderer;

pub struct EnumRenderManager;

impl EnumRenderManager {
  pub fn render_sync(
    definitions: &[EnumDefinition],
    language: Language,
  ) -> Result<Vec<Artifact>, String> {
    Ok(Self::renderer_for(language).render(definitions))
  }

  fn renderer_for(language: Language) -> Box<dyn EnumRenderer> {
    match language {
      Language::TypeScript => Box::new(TypeScriptEnumRenderer),
      Language::Php => Box::new(PhpEnumRenderer),
    }
  }
}
