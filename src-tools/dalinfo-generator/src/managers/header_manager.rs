use crate::models::enum_definition::{EnumDefinition, EnumKind, EnumMember};
use crate::services::casing_service::CasingService;
use serde_json::Value;

pub struct HeaderDefinition {
  pub canonical_name: String,
  pub name: String,
  pub enum_values: Vec<String>,
}

pub struct HeaderManager;

impl HeaderManager {

  /// Emits language-agnostic definitions; rendering is delegated to a per-language renderer.
  pub fn generate_header_enums_sync(swagger_json: &Value) -> Result<Vec<EnumDefinition>, String> {
    let headers = Self::collect_headers(swagger_json);
    let mut definitions = vec![Self::build_header_names_definition(&headers)];
    definitions.extend(Self::build_header_values_definitions(&headers));
    Ok(definitions)
  }

  fn build_header_names_definition(headers: &[HeaderDefinition]) -> EnumDefinition {
    EnumDefinition {
      kind: EnumKind::HeaderName,
      type_name: "HeaderNames".to_string(),
      file_base: "header-names".to_string(),
      members: headers
        .iter()
        .map(|header| EnumMember {
          source_name: header.canonical_name.clone(),
          value: header.name.clone(),
        })
        .collect(),
    }
  }

  fn build_header_values_definitions(headers: &[HeaderDefinition]) -> Vec<EnumDefinition> {
    headers
      .iter()
      .filter(|header| !header.enum_values.is_empty())
      .map(|header| EnumDefinition {
        kind: EnumKind::HeaderValue,
        type_name: format!("{}Values", CasingService::to_pascal_case(&header.name)),
        file_base: format!("header-values-{}", CasingService::to_kebab_case(&header.name)),
        members: header
          .enum_values
          .iter()
          .map(|value| EnumMember {
            source_name: value.clone(),
            value: value.clone(),
          })
          .collect(),
      })
      .collect()
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