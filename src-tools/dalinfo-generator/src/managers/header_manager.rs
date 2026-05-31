use crate::services::io_service::IOService;
use serde_json::Value;

pub struct HeaderManager;

impl HeaderManager {

    pub fn generate_headers_names_sync(
        swagger_json: &Value,
        output_dir: &str,
    ) -> Result<(), String> {
        let mut headers: Vec<(String, String)> = vec![];

        if let Some(obj) = swagger_json["components"]["headers"].as_object() {
            for (key, _) in obj {
                headers.push((
                    key.to_string(),
                    key.clone(),
                ));
            }
        }


        if let Some(params) = swagger_json["components"]["parameters"].as_object() {
            for (_, param) in params {
                let location = param["in"].as_str().unwrap_or("");
                if location == "header" {
                    let name = param["name"]
                        .as_str()
                        .unwrap_or("")
                        .to_string();
                    headers.push((
                        name.clone(),
                        name.clone(),
                    ));
                }
            }
        }

        let mut lines = String::new();

        lines.push_str("export const enum HeaderNames {\n");

        for (key, _) in headers {

            let const_name = Self::to_screaming_snake_case(&key);
            let value = key.clone();

            lines.push_str(&format!(
                "  {} = \"{}\",\n",
                const_name,
                value
            ));
        }

        lines.push_str("}\n");

        let output_path = format!("{}/headers/names/header-names.enum.ts", output_dir);

        IOService::write_file_sync(
            &output_path,
            &lines,
        )?;

        Ok(())
    }

    pub fn generate_header_values_sync(
        swagger_json: &Value,
        output_dir: &str,
    ) -> Result<(), String> {

    let mut index_exports: Vec<String> = vec![];

    if let Some(obj) = swagger_json["components"]["headers"].as_object() {

        for (key, def) in obj {

            if let Some(enum_vals) =
                def["schema"]["enum"].as_array()
            {
                let values = Self::extract_enum_values(enum_vals);

                if values.is_empty() {
                    continue;
                }

                let file_base =
                    Self::to_kebab_case(key);

                let enum_name =
                    Self::to_pascal_case(key);

                let file_name = format!(
                    "header-values-{}.enum.ts",
                    file_base
                );

                let content =
                    Self::render_enum(&enum_name, &values);

                let path = format!("{}/{}", output_dir, file_name);

                IOService::write_file_sync(&path, &content)?;

                index_exports.push(format!(
                    "export * from './{}';",
                    file_name.replace(".ts", "")
                ));
            }
        }
    }

    if let Some(params) = swagger_json["components"]["parameters"].as_object() {

        for (_, param) in params {

            let location = param["in"].as_str().unwrap_or("");

            if location != "header" {
                continue;
            }

            if let Some(enum_vals) =
                param["schema"]["enum"].as_array()
            {
                let values = Self::extract_enum_values(enum_vals);

                if values.is_empty() {
                    continue;
                }

                let name = param["name"].as_str().unwrap_or("");

                let file_base =
                    Self::to_kebab_case(name);

                let enum_name =
                    Self::to_pascal_case(name);

                let file_name = format!(
                    "header-values-{}.enum.ts",
                    file_base
                );

                let content =
                    Self::render_enum(&enum_name, &values);

                let path = format!("{}/headers/values/{}", output_dir, file_name);

                IOService::write_file_sync(&path, &content)?;

                index_exports.push(format!(
                    "export * from './{}';",
                    file_name.replace(".ts", "")
                ));
            }
        }
    }


    let index_content = index_exports.join("\n");

    let index_path = format!("{}/headers/values/index.ts", output_dir);

    IOService::write_file_sync(&index_path, &index_content)?;

    Ok(())
  }

  fn render_enum(name: &str, values: &[String]) -> String {
    let mut out = String::new();

    out.push_str(&format!("export enum {}Values {{\n", name));

    for v in values {

        let key = Self::to_pascal_case(v);

        out.push_str(&format!(
            "  {} = \"{}\",\n",
            key, v
        ));
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
}