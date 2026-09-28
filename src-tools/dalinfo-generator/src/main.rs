mod models {
  pub mod artifact;
  pub mod enum_definition;
  pub mod language;
}

mod services {
  pub mod casing_service;
  pub mod io_service;
  pub mod yaml_service;
  pub mod yaml_to_json_service;
}

mod renderers {
  pub mod enum_renderer;
  pub mod php_enum_renderer;
  pub mod typescript_enum_renderer;
}

mod managers {
  pub mod io_manager;
  pub mod yaml_manager;
  pub mod path_manager;
  pub mod header_manager;
  pub mod enum_render_manager;
  pub mod yaml_to_json_manager;
  pub mod dto_manager;
  pub mod operation_id_manager;
}

use managers::{
  io_manager::IOManager,
  yaml_manager::YamlManager,
  path_manager::PathManager,
  header_manager::HeaderManager,
  enum_render_manager::EnumRenderManager,
  yaml_to_json_manager::YamlToJsonManager,
  dto_manager::DTOManager,
  operation_id_manager::OperationIdManager,
};

use std::env;
use std::path::Path;
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
  let mut artifacts = EnumRenderManager::render_sync(&headerenums, language).expect("header render failed");

  if language == Language::TypeScript {
    artifacts.extend(build_typescript_paths_and_dtos(&json));
  }

  for artifact in artifacts {
    let _ = IOManager::write_file_sync(&format!("{}/{}", &outputfolder, &artifact.path), &artifact.content);
  };
}

fn build_typescript_paths_and_dtos(json: &serde_json::Value) -> Vec<Artifact> {
  let pathfiles = PathManager::generate_paths_sync(json).expect("path generation failed");
  let dtofiles = DTOManager::create_dtos(json, "TYPESCRIPT").expect("header dto generation failed");

  let basepath_paths = "paths";
  let basepath_dtofiles = "dtos";

  let mut artifacts = build_indexes(&pathfiles, &dtofiles);
  artifacts.extend(
    pathfiles.into_iter().map(|m| Artifact {
      path: format!("{}/{}", basepath_paths, m.path),
      ..m
    })
  );
  artifacts.extend(
    dtofiles.into_iter().map(|m| Artifact {
      path: format!("{}/{}", basepath_dtofiles, m.path),
      ..m
    })
  );
  artifacts
}

fn build_indexes(
  header_path_files: &[Artifact],
  dto_files: &[Artifact],
) -> Vec<Artifact> {
  let paths_exports = header_path_files
    .iter()
    .filter_map(|a| {
      Path::new(&a.path)
        .file_stem()
        .map(|s| format!("export * from \"./{}\";", s.to_string_lossy()))
      })
    .collect::<Vec<_>>()
    .join("\n");

  let dto_request_exports = build_nested_exports(dto_files, "request");
  let dto_response_exports = build_nested_exports(dto_files, "response");

  vec![
    Artifact {
      path: "paths/index.ts".into(),
      content: paths_exports,
    },
    Artifact {
      path: "index.ts".into(),
      content: "export * from \"./headers\";\nexport * from \"./paths\";\nexport * from \"./dtos\";".into(),
    },
    Artifact {
      path: "dtos/index.ts".into(),
      content: "export * from \"./request\";\nexport * from \"./response\";".into(),
    },
    Artifact {
      path: "dtos/request/index.ts".into(),
      content: dto_request_exports,
    },
    Artifact {
      path: "dtos/response/index.ts".into(),
      content: dto_response_exports,
    },
  ]
}

fn build_nested_exports(files: &[Artifact], folder: &str) -> String {
  files
    .iter()
    .filter_map(|artifact| {
      let path = Path::new(&artifact.path);
      let parent = path.parent()?.to_string_lossy();

      if parent != folder {
        return None;
      }

      path.file_stem()
        .map(|stem| format!("export * from \"./{}\";", stem.to_string_lossy()))
    })
    .collect::<Vec<_>>()
    .join("\n")
}
