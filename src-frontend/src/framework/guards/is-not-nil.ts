import { isNilCore } from "./internal";

export function iNotNil(valueLike: unknown): valueLike is undefined | null {
  return !isNilCore(valueLike);
}
