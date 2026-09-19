use crate::models::artifact::Artifact;
use serde_json::Value;

pub struct HeaderDefinition {
  pub canonical_name: String,
  pub name: String,
  pub enum_values: Vec<String>,
}

pub struct HeaderManager;

impl HeaderManager {

  pub fn generate_headers_names_sync(swagger_json: &Value) -> Result<Vec<Artifact>, String> {
    let mut artifacts: Vec<Artifact> = vec![];
    let headers = Self::collect_headers(swagger_json);
    let mut content = String::new();
    content.push_str("export const enum HeaderNames {\n");

    for header in headers {
      let const_name = Self::to_screaming_snake_case(&header.canonical_name);
      content.push_str(&format!("  {} = \"{}\",\n", const_name, header.name));
    }

    content.push_str("}\n");

    let path = format!("header-names.enum.ts");
    let artifact = Artifact { path, content };
    artifacts.push(artifact);
    Ok(artifacts)
  }

  pub fn generate_header_values_sync(swagger_json: &Value) -> Result<Vec<Artifact>, String> {
    let mut artifacts: Vec<Artifact> = vec![];
    let headers = Self::collect_headers(swagger_json);

    for header in headers {
        if header.enum_values.is_empty() {
          continue;
        }

        let file_base = Self::to_kebab_case(&header.name);
        let enum_name = Self::to_pascal_case(&header.name);

        let content = Self::render_enum(&enum_name, &header.enum_values,);
        let path = format!("header-values-{}.enum.ts",file_base);

        let artifact = Artifact { path, content };
        artifacts.push(artifact);
    }

    Ok(artifacts)
}

  fn render_enum(name: &str, values: &[String]) -> String {
    let mut out = String::new();
    out.push_str(&format!("export enum {}Values {{\n", name));

    for v in values {
      let key = Self::to_pascal_case(v);
      out.push_str(&format!("  {} = \"{}\",\n", key, v));
    }

    out.push_str("}\n");
    out
  }

  fn to_pascal_case(input: &str) -> String {
    input
      .split(|c: char| {
        c == '-' || c == '_' || c == ' ' || c == '/' || c == '.'
      })
      .filter(|s| !s.is_empty())
      .map(|w| {
        let mut c = w.chars();
        match c.next() {
          None => String::new(),
          Some(f) => f.to_uppercase().collect::<String>() + c.as_str(),
        }
      })
      .collect()
  }

  fn to_screaming_snake_case(input: &str) -> String {
    input
      .split(|c: char| c == '-' || c == '_' || c == ' ')
      .filter(|s| !s.is_empty())
      .map(|s| s.to_uppercase())
      .collect::<Vec<_>>()
      .join("_")
  }

  fn to_kebab_case(input: &str) -> String {
    input
      .split(|c: char| c == '-' || c == '_' || c == ' ' || c == '/')
      .filter(|s| !s.is_empty())
      .map(|s| s.to_lowercase())
      .collect::<Vec<_>>()
      .join("-")
  }

  fn extract_enum_values(arr: &Vec<Value>) -> Vec<String> {
    arr.iter()
      .filter_map(|v| v.as_str().map(|s| s.to_string()))
      .collect()
  }

  fn collect_headers(
    swagger_json: &Value,
  ) -> Vec<HeaderDefinition> {
    let mut headers: Vec<HeaderDefinition> = vec![];

    if let Some(obj) = swagger_json["components"]["headers"].as_object()
    {
      for (key, def) in obj {
        let enum_values = def["schema"]["enum"]
          .as_array()
          .map(Self::extract_enum_values)
          .unwrap_or_default();

        headers.push(
          HeaderDefinition {
            canonical_name: key.clone(),
            name: key.clone(),
            enum_values,
          }
        );
      }
    }

    if let Some(params) = swagger_json["components"]["parameters"].as_object()
    {
      for (key, param) in params {
        let location = param["in"]
          .as_str()
          .unwrap_or("");

        if location != "header" {
          continue;
        }

        let name = param["name"]
          .as_str()
          .unwrap_or("")
          .to_string();

        let enum_values = param["schema"]["enum"]
          .as_array()
          .map(Self::extract_enum_values)
          .unwrap_or_default();

        headers.push(
          HeaderDefinition {
            canonical_name: key.clone(),
            name,
            enum_values,
          }
        );
      }
    }
    headers
  }
}