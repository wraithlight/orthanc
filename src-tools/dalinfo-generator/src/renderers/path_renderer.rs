use crate::models::artifact::Artifact;
use crate::models::path_definition::PathDefinition;

pub trait PathRenderer {
  fn render(&self, definitions: &[PathDefinition]) -> Vec<Artifact>;
}
