use crate::models::artifact::Artifact;
use serde_json::Value;
use regex::Regex;

pub struct PathManager;

impl PathManager {
  pub fn generate_paths_sync(swagger_json: &Value) -> Result<Vec<Artifact>, String> {
    let paths = swagger_json["paths"]
      .as_object()
      .ok_or("Missing paths section")?;
    let param_regex = Regex::new(r"\{([^}]+)\}").unwrap();
    let mut artifacts: Vec<Artifact> = vec![];

    for (endpoint_path, methods) in paths {
      let methods_obj = methods
        .as_object()
        .ok_or("Invalid methods object")?;

      for (_, operation) in methods_obj {
        let operation_id = operation["operationId"]
          .as_str()
          .ok_or("Missing operationId")?;

        let file_name = Self::to_kebab_case(operation_id);
        let const_name = Self::to_path_const_name(operation_id);

        let mut params: Vec<String> = vec![];
        for cap in param_regex.captures_iter(endpoint_path) {
          params.push(cap[1].to_string());
        }

        let args = if params.is_empty() {
          String::new()
        } else {
          params
            .iter()
            .map(|p| format!("{}: string", p))
            .collect::<Vec<_>>()
            .join(", ")
        };

        let mut interpolated_path = endpoint_path.to_string();

        for param in &params {
          interpolated_path = interpolated_path.replace(
            &format!("{{{}}}", param),
            &format!("${{{}}}", param)
          );
        }

        let content = if params.is_empty() {
          format!(
            r#"export const {} = () => `{}`;"#,
            const_name, endpoint_path
          )
        } else {
          format!(
            r#"export const {} = ({}) => `{}`;"#,
            const_name, args, interpolated_path
          )
        };

        let path = format!("{}.path.const.ts", file_name);

        let artifact = Artifact { path, content };
        artifacts.push(artifact);
      }
    }

    Ok(artifacts)
  }

  fn to_kebab_case(input: &str) -> String {
    let mut result = String::new();
    for (i, c) in input.chars().enumerate() {
      if c.is_uppercase() && i != 0 {
        result.push('-');
      }
      result.push(c.to_ascii_lowercase());
    }
    result
  }

  fn to_path_const_name(operation_id: &str) -> String {
    let mut screaming_snake = String::new();
    for (i, c) in operation_id.chars().enumerate() {
      if c.is_uppercase() && i != 0 {
        screaming_snake.push('_');
      }
      screaming_snake.push(c.to_ascii_uppercase());
    }
    format!("API_{}_PATH", screaming_snake)
  }
}
