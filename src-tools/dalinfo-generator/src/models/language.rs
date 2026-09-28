#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum Language {
  TypeScript,
  Php,
}

impl Language {
  pub fn parse(input: &str) -> Result<Self, String> {
    match input.to_uppercase().as_str() {
      "TYPESCRIPT" | "TS" => Ok(Language::TypeScript),
      "PHP" => Ok(Language::Php),
      other => Err(format!("Unsupported language: {}", other)),
    }
  }
}
