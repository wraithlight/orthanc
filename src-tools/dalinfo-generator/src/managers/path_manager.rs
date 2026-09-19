use crate::models::artifact::Artifact;
use serde_json::Value;
use regex::Regex;

pub struct PathManager;

struct QueryParam {
  name: String,
  arg_name: String,
  required: bool,
}

impl PathManager {
  pub fn generate_paths_sync(swagger_json: &Value) -> Result<Vec<Artifact>, String> {
    let paths = swagger_json["paths"]
      .as_object()
      .ok_or("Missing paths section")?;
    let components_params = swagger_json["components"]["parameters"].as_object();
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

        let mut path_params: Vec<String> = vec![];
        for cap in param_regex.captures_iter(endpoint_path) {
          path_params.push(cap[1].to_string());
        }

        let query_params = Self::collect_query_params(operation, components_params);

        let mut args: Vec<String> = path_params
          .iter()
          .map(|p| format!("{}: string", p))
          .collect();
        args.extend(query_params.iter().map(|p| {
          if p.required {
            format!("{}: string", p.arg_name)
          } else {
            format!("{}?: string", p.arg_name)
          }
        }));
        let args_str = args.join(", ");

        let mut interpolated_path = endpoint_path.to_string();

        for param in &path_params {
          interpolated_path = interpolated_path.replace(
            &format!("{{{}}}", param),
            &format!("${{{}}}", param)
          );
        }

        let content = if args.is_empty() {
          format!(
            r#"export const {} = () => `{}` as const;"#,
            const_name, endpoint_path
          )
        } else if query_params.is_empty() {
          format!(
            r#"export const {} = ({}) => `{}` as const;"#,
            const_name, args_str, interpolated_path
          )
        } else {
          Self::build_content_with_query(&const_name, &args_str, &interpolated_path, &query_params)
        };

        let path = format!("{}.path.const.ts", file_name);

        let artifact = Artifact { path, content };
        artifacts.push(artifact);
      }
    }

    Ok(artifacts)
  }

  fn collect_query_params(
    operation: &Value,
    components_params: Option<&serde_json::Map<String, Value>>,
  ) -> Vec<QueryParam> {
    operation["parameters"]
      .as_array()
      .map(|params| {
        params
          .iter()
          .map(|param| Self::resolve_parameter(param, components_params))
          .filter(|param| param["in"].as_str() == Some("query"))
          .map(|param| {
            let name = param["name"].as_str().unwrap_or_default().to_string();
            let arg_name = Self::to_camel_case(&name);
            let required = param["required"].as_bool().unwrap_or(false);
            QueryParam { name, arg_name, required }
          })
          .collect()
      })
      .unwrap_or_default()
  }

  fn resolve_parameter(
    param: &Value,
    components_params: Option<&serde_json::Map<String, Value>>,
  ) -> Value {
    if let Some(reference) = param["$ref"].as_str() {
      let name = reference.rsplit('/').next().unwrap_or(reference);
      if let Some(resolved) = components_params.and_then(|params| params.get(name)) {
        return resolved.clone();
      }
    }
    param.clone()
  }

  fn build_content_with_query(
    const_name: &str,
    args_str: &str,
    interpolated_path: &str,
    query_params: &[QueryParam],
  ) -> String {
    let mut body = String::from("  const queryParams = new URLSearchParams();\n");

    for param in query_params {
      let set_call = format!(
        "  queryParams.set(\"{}\", {});\n",
        param.name, param.arg_name
      );

      if param.required {
        body.push_str(&set_call);
      } else {
        body.push_str(&format!(
          "  if ({} !== undefined) {{\n  {}  }}\n",
          param.arg_name, set_call
        ));
      }
    }

    body.push_str(&format!(
      "  return `{}?${{queryParams.toString()}}` as const;\n",
      interpolated_path
    ));

    format!(
      "export const {} = ({}) => {{\n{}}};",
      const_name, args_str, body
    )
  }

  fn to_camel_case(input: &str) -> String {
    let mut result = String::new();
    let mut capitalize_next = false;

    for c in input.chars() {
      if c == '-' || c == '_' {
        capitalize_next = true;
        continue;
      }

      if capitalize_next {
        result.extend(c.to_uppercase());
        capitalize_next = false;
      } else {
        result.push(c);
      }
    }

    result
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
