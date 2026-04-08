import { isNilCore } from "./internal";

export function isNil(valueLike: unknown): valueLike is undefined | null {
  return isNilCore(valueLike);
}
