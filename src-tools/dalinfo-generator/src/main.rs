mod models {
  pub mod artifact;
}

mod services {
  pub mod io_service;
  pub mod yaml_service;
  pub mod yaml_to_json_service;
}

mod managers {
  pub mod io_manager;
  pub mod yaml_manager;
  pub mod path_manager;
  pub mod header_manager;
  pub mod yaml_to_json_manager;
}

use managers::{
  io_manager::IOManager,
  yaml_manager::YamlManager,
  path_manager::PathManager,
  header_manager::HeaderManager,
  yaml_to_json_manager::YamlToJsonManager,
};

use std::env;
use crate::models::artifact::Artifact;

fn main() {
  let args: Vec<String> = env::args().collect();

  if args.len() != 4 {
    panic!(
      "Usage: <inputYaml> <outputDir> <language>"
    );
  }

  let inputfile = &args[1];
  let outputfolder = &args[2];

  IOManager::cleanup_output_dir_sync(outputfolder).expect("Output cleanup failed!");
  let text = IOManager::read_file_sync(inputfile).expect("Reading input file failed!");
  let yaml = YamlManager::parse_yaml_sync(&text).expect("yaml parse failed");
  let json = YamlToJsonManager::yaml_to_json_sync(yaml).expect("yaml->json failed");

  let pathfiles = PathManager::generate_paths_sync(&json).expect("path generation failed");
  let headernamefiles = HeaderManager::generate_headers_names_sync(&json).expect("header name generation failed");
  let headervaluesfiles = HeaderManager::generate_header_values_sync(&json).expect("header value generation failed");

  let mut artifacts = Vec::new();

  let basepath_paths = "paths";
  let basepath_headernames = "headers/names";
  let basepath_headervalues = "headers/values";
  artifacts.extend(self::build_indexes(&headernamefiles, &headervaluesfiles, &pathfiles));
  artifacts.extend(
    pathfiles.into_iter().map(|m| Artifact {
      path: format!("{}/{}", basepath_paths, m.path),
      ..m
    })
  );
  artifacts.extend(
    headernamefiles.into_iter().map(|m| Artifact {
      path: format!("{}/{}", basepath_headernames, m.path),
      ..m
    })
  );
  artifacts.extend(
    headervaluesfiles.into_iter().map(|m| Artifact {
      path: format!("{}/{}", basepath_headervalues, m.path),
      ..m
    })
  );

  for artifact in artifacts {
    let _ = IOManager::write_file_sync(&format!("{}/{}", &outputfolder, &artifact.path), &artifact.content);
  };
}

fn build_indexes(
  header_name_files: &[Artifact],
  header_value_files: &[Artifact],
  header_path_files: &[Artifact],
) -> Vec<Artifact> {
  let names_exports = header_name_files
    .iter()
    .filter_map(|a| {
      std::path::Path::new(&a.path)
        .file_stem()
        .map(|s| format!("export * from \"./{}\";", s.to_string_lossy()))
      })
    .collect::<Vec<_>>()
    .join("\n");

  let values_exports = header_value_files
    .iter()
    .filter_map(|a| {
      std::path::Path::new(&a.path)
        .file_stem()
        .map(|s| format!("export * from \"./{}\";", s.to_string_lossy()))
      })
    .collect::<Vec<_>>()
    .join("\n");

  let paths_exports = header_path_files
    .iter()
    .filter_map(|a| {
      std::path::Path::new(&a.path)
        .file_stem()
        .map(|s| format!("export * from \"./{}\";", s.to_string_lossy()))
      })
    .collect::<Vec<_>>()
    .join("\n");

  vec![
    Artifact {
      path: "headers/index.ts".into(),
      content: "export * from \"./names\";\nexport * from \"./values\";".into(),
    },
    Artifact {
      path: "headers/names/index.ts".into(),
      content: names_exports,
    },
    Artifact {
      path: "headers/values/index.ts".into(),
      content: values_exports,
    },
    Artifact {
      path: "paths/index.ts".into(),
      content: paths_exports,
    },
    Artifact {
      path: "index.ts".into(),
      content: "export * from \"./headers\";\nexport * from \"./paths\";".into(),
    },
  ]
}
