use serde_json::Value as JsonValue;
use serde_yaml::Value as YamlValue;

pub struct JsonManager;

impl JsonManager {
  pub fn yaml_to_json_sync(
    yaml: YamlValue,
  ) -> Result<JsonValue, String> {
    serde_json::to_value(yaml)
      .map_err(|e| {
        format!("JSON conversion error: {}", e)
      })
  }
}
