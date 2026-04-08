import { isNilCore } from "./internal";

export function isNotNil(valueLike: unknown): valueLike is undefined | null {
  return !isNilCore(valueLike);
}
