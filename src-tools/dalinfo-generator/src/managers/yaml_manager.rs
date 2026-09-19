use crate::services::yaml_service::YamlService;
use serde_yaml::Value;

pub struct YamlManager;

impl YamlManager {
  pub fn parse_yaml_sync(
    yaml: &str,
  ) -> Result<Value, String> {
    YamlService::parse_yaml_sync(yaml)
  }
}
