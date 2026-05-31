use crate::services::io_service::IOService;
use serde_json::Value;

pub struct PathManager;

impl PathManager {

    pub fn generate_paths_sync(
        swagger_json: &Value,
        output_dir: &str,
    ) -> Result<(), String> {

        let paths = swagger_json["paths"]
            .as_object()
            .ok_or("Missing paths section")?;

        let mut index_exports: Vec<String> = vec![];
        for (endpoint_path, methods) in paths {

            let methods_obj = methods
                .as_object()
                .ok_or("Invalid methods object")?;

            for (_, operation) in methods_obj {

                let operation_id = operation["operationId"]
                    .as_str()
                    .ok_or("Missing operationId")?;

                let const_name =
                    Self::to_screaming_snake_case(operation_id);

                let file_name =
                    Self::to_kebab_case(operation_id);

                let filename = format!(
                    "{}.path.const.ts",
                    file_name
                );

                let content = format!(
r#"export const {}_PATH = "{}";
"#,
                    const_name,
                    endpoint_path
                );

                let abs_output_path =
                    format!("{}/paths/{}", output_dir, filename);

                IOService::write_file_sync(
                    &abs_output_path,
                    &content,
                )?;

                index_exports.push(format!(
                    "export * from './{}.path.const';",
                    file_name
                ));
            }
        }

        let index_content = index_exports.join("\n");

        let index_path = format!("{}/paths/index.ts", output_dir);

        IOService::write_file_sync(
            &index_path,
            &index_content,
        )?;

        Ok(())
    }

    fn to_screaming_snake_case(input: &str) -> String {
        let mut result = String::new();

        for (i, c) in input.chars().enumerate() {
            if c.is_uppercase() && i != 0 {
                result.push('_');
            }
            result.push(c.to_ascii_uppercase());
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
}