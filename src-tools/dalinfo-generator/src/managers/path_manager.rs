use crate::models::path_definition::{PathDefinition, PathParameter, PathSegment};
use crate::services::casing_service::CasingService;
use serde_json::Value;
use regex::Regex;

pub struct PathManager;

impl PathManager {
  /// Emits language-agnostic definitions; rendering is delegated to a per-language renderer.
  pub fn generate_paths_sync(swagger_json: &Value) -> Result<Vec<PathDefinition>, String> {
    let paths = swagger_json["paths"]
      .as_object()
      .ok_or("Missing paths section")?;
    let components_params = swagger_json["components"]["parameters"].as_object();
    let param_regex = Regex::new(r"\{([^}]+)\}").unwrap();
    let mut definitions: Vec<PathDefinition> = vec![];

    for (endpoint_path, methods) in paths {
      let methods_obj = methods
        .as_object()
        .ok_or("Invalid methods object")?;

      for (_, operation) in methods_obj {
        let operation_id = operation["operationId"]
          .as_str()
          .ok_or("Missing operationId")?;

        let segments = Self::split_segments(endpoint_path, &param_regex);
        let path_params = Self::collect_path_params(&segments);
        let query_params = Self::collect_query_params(operation, components_params);

        definitions.push(PathDefinition {
          operation_id: operation_id.to_string(),
          file_base: CasingService::camel_to_kebab_case(operation_id),
          segments,
          path_params,
          query_params,
        });
      }
    }

    Ok(definitions)
  }

  fn split_segments(endpoint_path: &str, param_regex: &Regex) -> Vec<PathSegment> {
    let mut segments: Vec<PathSegment> = vec![];
    let mut cursor = 0;

    for capture in param_regex.captures_iter(endpoint_path) {
      let whole = capture.get(0).unwrap();

      if whole.start() > cursor {
        segments.push(PathSegment::Literal(endpoint_path[cursor..whole.start()].to_string()));
      }

      segments.push(PathSegment::Param(capture[1].to_string()));
      cursor = whole.end();
    }

    if cursor < endpoint_path.len() {
      segments.push(PathSegment::Literal(endpoint_path[cursor..].to_string()));
    }

    segments
  }

  fn collect_path_params(segments: &[PathSegment]) -> Vec<PathParameter> {
    segments
      .iter()
      .filter_map(|segment| match segment {
        PathSegment::Param(name) => Some(PathParameter {
          name: name.clone(),
          arg_name: CasingService::to_camel_case(name),
          required: true,
        }),
        PathSegment::Literal(_) => None,
      })
      .collect()
  }

  fn collect_query_params(
    operation: &Value,
    components_params: Option<&serde_json::Map<String, Value>>,
  ) -> Vec<PathParameter> {
    operation["parameters"]
      .as_array()
      .map(|params| {
        params
          .iter()
          .map(|param| Self::resolve_parameter(param, components_params))
          .filter(|param| param["in"].as_str() == Some("query"))
          .map(|param| {
            let name = param["name"].as_str().unwrap_or_default().to_string();
            let arg_name = CasingService::to_camel_case(&name);
            let required = param["required"].as_bool().unwrap_or(false);
            PathParameter { name, arg_name, required }
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
}
