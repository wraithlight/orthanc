use crate::models::artifact::Artifact;
use crate::models::enum_definition::EnumDefinition;

pub trait EnumRenderer {
  fn render(&self, definitions: &[EnumDefinition]) -> Vec<Artifact>;
}
