pub struct CasingService;

impl CasingService {
  pub fn to_pascal_case(input: &str) -> String {
    Self::split_words(input)
      .into_iter()
      .map(|word| {
        // all-uppercase words (REQUESTID, X-ORTHANC-PLATFORM) would otherwise stay shouting
        let normalized = if word.chars().all(|c| !c.is_lowercase()) {
          word.to_lowercase()
        } else {
          word.to_string()
        };
        let mut chars = normalized.chars();
        match chars.next() {
          None => String::new(),
          Some(first) => first.to_uppercase().collect::<String>() + chars.as_str(),
        }
      })
      .collect()
  }

  pub fn to_screaming_snake_case(input: &str) -> String {
    Self::split_words(input)
      .into_iter()
      .map(|word| word.to_uppercase())
      .collect::<Vec<_>>()
      .join("_")
  }

  pub fn to_kebab_case(input: &str) -> String {
    Self::split_words(input)
      .into_iter()
      .map(|word| word.to_lowercase())
      .collect::<Vec<_>>()
      .join("-")
  }

  pub fn to_camel_case(input: &str) -> String {
    let pascal = Self::to_pascal_case(input);
    let mut chars = pascal.chars();
    match chars.next() {
      None => String::new(),
      Some(first) => first.to_lowercase().collect::<String>() + chars.as_str(),
    }
  }

  pub fn camel_to_kebab_case(input: &str) -> String {
    Self::split_camel_humps(input, '-').to_lowercase()
  }

  pub fn camel_to_screaming_snake_case(input: &str) -> String {
    Self::split_camel_humps(input, '_').to_uppercase()
  }

  pub fn sanitize_identifier(input: &str) -> String {
    let mut out: String = input.chars().filter(|c| c.is_ascii_alphanumeric()).collect();

    if out.is_empty() {
      return "GeneratedType".to_string();
    }

    if out.chars().next().map(|c| c.is_ascii_digit()).unwrap_or(false) {
      out = format!("Type{}", out);
    }

    out
  }

  fn split_camel_humps(input: &str, separator: char) -> String {
    let mut result = String::new();
    for (index, character) in input.chars().enumerate() {
      if character.is_uppercase() && index != 0 {
        result.push(separator);
      }
      result.push(character);
    }
    result
  }

  fn split_words(input: &str) -> Vec<&str> {
    input
      .split(|c: char| c == '-' || c == '_' || c == ' ' || c == '/' || c == '.')
      .filter(|part| !part.is_empty())
      .collect()
  }
}
