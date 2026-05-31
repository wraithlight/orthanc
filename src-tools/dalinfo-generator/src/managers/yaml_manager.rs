use crate::services::io_service::IOService;
use serde_yaml::Value;

pub struct YamlManager;

impl YamlManager {
  pub fn read_yaml_sync(
    abs_path: &str,
  ) -> Result<String, String> {
    IOService::read_file_sync(abs_path)
  }

  pub fn parse_yaml_sync(
    yaml_text: &str,
  ) -> Result<Value, String> {
    serde_yaml::from_str(yaml_text)
    .map_err(|e| format!("YAML parse error: {}", e))
  }
}
