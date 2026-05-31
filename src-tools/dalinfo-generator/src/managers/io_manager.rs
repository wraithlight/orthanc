use crate::services::io_service::IOService;

pub struct IOManager;

impl IOManager {
  pub fn cleanup_output_dir_sync(
    dir: &str,
  ) -> Result<(), String> {
   IOService::clear_dir_sync(dir)
  }

  pub fn read_file_sync(
    path: &str,
  ) -> Result<String, String> {
    IOService::read_file_sync(path)
  }

  pub fn write_file_sync(
    path: &str,
    content: &str
  ) -> Result<(), String> {
    IOService::write_file_sync(path, content)
  }
}
