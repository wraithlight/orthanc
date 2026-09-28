#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum EnumKind {
  HeaderName,
  HeaderValue,
}

#[derive(Debug, Clone)]
pub struct EnumMember {
  pub source_name: String,
  pub value: String,
}

#[derive(Debug, Clone)]
pub struct EnumDefinition {
  pub kind: EnumKind,
  pub type_name: String,
  pub file_base: String,
  pub members: Vec<EnumMember>,
}
