use crate::models::artifact::Artifact;
use crate::models::dto_definition::OperationAst;

pub trait DTORenderer {
  fn render(&self, operations: &[OperationAst]) -> Vec<Artifact>;
}
