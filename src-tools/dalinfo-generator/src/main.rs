mod services {
  pub mod io_service;
}

mod managers {
  pub mod yaml_manager;
  pub mod json_manager;
  pub mod path_manager;
  pub mod header_manager;
}

use crate::services::io_service::IOService;

use managers::{
  json_manager::JsonManager,
  yaml_manager::YamlManager,
  path_manager::PathManager,
  header_manager::HeaderManager,
};


use std::env;

fn main() {
  let args: Vec<String> = env::args().collect();

  if args.len() != 4 {
    panic!(
      "Usage: <inputYaml> <outputDir> <language>"
    );
  }

    let input_file = &args[1];
    let output_dir = &args[2];

  IOService::clear_dir_sync(output_dir)
    .expect("output cleanup failed");
   
    let yaml_text =
        YamlManager::read_yaml_sync(input_file)
            .expect("yaml read failed");

    let parsed_yaml =
        YamlManager::parse_yaml_sync(&yaml_text)
            .expect("yaml parse failed");

    let json =
        JsonManager::yaml_to_json_sync(
            parsed_yaml
        )
        .expect("yaml->json failed");

    PathManager::generate_paths_sync(
        &json,
        output_dir,
    )
    .expect("path generation failed");

    HeaderManager::generate_headers_names_sync(
      &json,
      output_dir
    )
    .expect("header name generation failed");

    HeaderManager::generate_header_values_sync(
      &json,
      output_dir
    )
    .expect("header value generation failed");
}
