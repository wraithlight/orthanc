use serde_json::Value;

pub struct OperationIdManager;

impl OperationIdManager {
  pub fn validate_operation_ids_sync(swagger_json: &Value) -> Result<(), String> {
    let paths = swagger_json["paths"]
      .as_object()
      .ok_or("Missing paths section")?;

    let mut missing: Vec<String> = vec![];

    for (endpoint_path, methods) in paths {
      let methods_obj = methods
        .as_object()
        .ok_or("Invalid methods object")?;

      for (method, operation) in methods_obj {
        if !matches!(
          method.as_str(),
          "get" | "post" | "put" | "delete" | "patch" | "head" | "options" | "trace"
        ) {
          continue;
        }

        let has_operation_id = operation
          .get("operationId")
          .and_then(|v| v.as_str())
          .map(|s| !s.trim().is_empty())
          .unwrap_or(false);

        if !has_operation_id {
          missing.push(format!("{} {}", method.to_uppercase(), endpoint_path));
        }
      }
    }

    if missing.is_empty() {
      return Ok(());
    }

    missing.sort();

    Err(format!(
      "Missing operationId for {} endpoint(s):\n{}",
      missing.len(),
      missing.join("\n")
    ))
  }
}
