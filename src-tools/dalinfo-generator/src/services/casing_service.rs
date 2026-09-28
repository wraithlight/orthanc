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

  fn split_words(input: &str) -> Vec<&str> {
    input
      .split(|c: char| c == '-' || c == '_' || c == ' ' || c == '/' || c == '.')
      .filter(|part| !part.is_empty())
      .collect()
  }
}
