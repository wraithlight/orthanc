use std::{
    fs,
    path::Path,
};

pub struct IOService;

impl IOService {
  pub fn read_file_sync(abs_path: &str) -> Result<String, String> {
    fs::read_to_string(abs_path)
      .map_err(|e| format!("Read error: {}", e))
  }

  pub fn write_file_sync(
    abs_path: &str,
    content: &str,
  ) -> Result<(), String> {
    let path = Path::new(abs_path);

    if let Some(parent) = path.parent() {
      fs::create_dir_all(parent)
        .map_err(|e| format!("Directory creation error: {}", e))?;
      }

    fs::write(path, content)
      .map_err(|e| format!("Write error: {}", e))
  }

  pub fn clear_dir_sync(dir: &str) -> Result<(), String> {
    if Path::new(dir).exists() {
      fs::remove_dir_all(dir)
        .map_err(|e| format!("Clear dir error: {}", e))?;
    }

    fs::create_dir_all(dir)
      .map_err(|e| format!("Recreate dir error: {}", e))?;

    Ok(())
  }
}
