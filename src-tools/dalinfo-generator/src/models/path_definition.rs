#[derive(Debug, Clone)]
pub enum PathSegment {
  Literal(String),
  Param(String),
}

#[derive(Debug, Clone)]
pub struct PathParameter {
  pub name: String,
  pub arg_name: String,
  pub required: bool,
}

#[derive(Debug, Clone)]
pub struct PathDefinition {
  pub operation_id: String,
  pub file_base: String,
  pub segments: Vec<PathSegment>,
  pub path_params: Vec<PathParameter>,
  pub query_params: Vec<PathParameter>,
}
