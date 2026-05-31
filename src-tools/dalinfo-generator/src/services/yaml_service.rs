use serde_yaml::Value as YamlValue;

pub struct YamlService;

impl YamlService {
  pub fn parse_yaml_sync(
    yaml_text: &str,
  ) -> Result<YamlValue, String> {
    serde_yaml::from_str(yaml_text)
      .map_err(|e| format!("YAML parse error: {}", e))
  }
}
