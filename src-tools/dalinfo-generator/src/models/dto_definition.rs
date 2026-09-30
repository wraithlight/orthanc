#[derive(Clone)]
pub enum Node {
  String,
  Number,
  Boolean,
  Array(Box<Node>),
  Union(Vec<Node>),
  StringLiteralUnion(Vec<String>),
  TypeRef(String),
  Dictionary(Box<Node>),
  Object {
    properties: Vec<Property>,
  },
}

#[derive(Clone)]
pub struct Property {
  pub name: String,
  pub required: bool,
  pub node: Node,
}

pub struct OperationAst {
  pub name: String,
  pub file_base: String,
  pub request_node: Option<Node>,
  pub response_node: Option<Node>,
}
