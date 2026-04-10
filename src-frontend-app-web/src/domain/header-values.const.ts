const APPLICATION_JSON = "application/json";

export const enum HeaderValuesContentType {
  ApplicationJson = APPLICATION_JSON,
}

export const enum HeaderValueAccept {
  ApplicationJson = APPLICATION_JSON,
}

/**
 * @public It's used but for some reason knip reports it as unused.
 */
export const enum Device {
  Mobile = "mobile",
  Desktop = "desktop",
}