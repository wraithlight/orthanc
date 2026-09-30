mod models {
  pub mod artifact;
  pub mod dto_definition;
  pub mod enum_definition;
  pub mod language;
  pub mod path_definition;
}

mod services {
  pub mod casing_service;
  pub mod io_service;
  pub mod yaml_service;
  pub mod yaml_to_json_service;
}

mod renderers {
  pub mod dto_renderer;
  pub mod enum_renderer;
  pub mod path_renderer;
  pub mod php_dto_renderer;
  pub mod php_enum_renderer;
  pub mod php_path_renderer;
  pub mod typescript_dto_renderer;
  pub mod typescript_enum_renderer;
  pub mod typescript_path_renderer;
}

mod managers {
  pub mod io_manager;
  pub mod yaml_manager;
  pub mod path_manager;
  pub mod header_manager;
  pub mod render_manager;
  pub mod yaml_to_json_manager;
  pub mod dto_manager;
  pub mod operation_id_manager;
}

use managers::{
  io_manager::IOManager,
  yaml_manager::YamlManager,
  path_manager::PathManager,
  header_manager::HeaderManager,
  render_manager::RenderManager,
  yaml_to_json_manager::YamlToJsonManager,
  dto_manager::DTOManager,
  operation_id_manager::OperationIdManager,
};

use std::env;
use crate::models::artifact::Artifact;
use crate::models::language::Language;

fn main() {
  let args: Vec<String> = env::args().collect();

  if args.len() != 4 {
    panic!(
      "Usage: <inputYaml> <outputDir> <language>"
    );
  }

  let inputfile = &args[1];
  let outputfolder = &args[2];
  let language = Language::parse(&args[3]).expect("language parse failed");

  IOManager::cleanup_output_dir_sync(outputfolder).expect("Output cleanup failed!");
  let text = IOManager::read_file_sync(inputfile).expect("Reading input file failed!");
  let yaml = YamlManager::parse_yaml_sync(&text).expect("yaml parse failed");
  let json = YamlToJsonManager::yaml_to_json_sync(yaml).expect("yaml->json failed");
  OperationIdManager::validate_operation_ids_sync(&json).expect("operationId validation failed");

  let headerenums = HeaderManager::generate_header_enums_sync(&json).expect("header collection failed");
  let pathdefinitions = PathManager::generate_paths_sync(&json).expect("path collection failed");
  let dtooperations = DTOManager::generate_dtos_sync(&json).expect("dto collection failed");

  let mut artifacts = RenderManager::render_enums_sync(&headerenums, language).expect("header render failed");
  artifacts.extend(RenderManager::render_paths_sync(&pathdefinitions, language).expect("path render failed"));
  artifacts.extend(RenderManager::render_dtos_sync(&dtooperations, language).expect("dto render failed"));

  if language == Language::TypeScript {
    artifacts.push(Artifact {
      path: "index.ts".into(),
      content: "export * from \"./headers\";\nexport * from \"./paths\";\nexport * from \"./dtos\";".into(),
    });
  }

  for artifact in artifacts {
    let _ = IOManager::write_file_sync(&format!("{}/{}", &outputfolder, &artifact.path), &artifact.content);
  };
}
