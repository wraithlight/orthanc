import { Guid } from "../../type";

export function newGuid(): Guid {
  return crypto.randomUUID();
}
