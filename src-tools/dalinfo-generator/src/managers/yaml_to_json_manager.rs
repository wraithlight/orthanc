use crate::services::yaml_to_json_service::YamlToJsonService;
use serde_json::Value as JsonValue;
use serde_yaml::Value as YamlValue;

pub struct YamlToJsonManager;

impl YamlToJsonManager {
  pub fn yaml_to_json_sync(
    yaml: YamlValue,
  ) -> Result<JsonValue, String> {
    YamlToJsonService::yaml_to_json_sync(yaml)
  }
}
