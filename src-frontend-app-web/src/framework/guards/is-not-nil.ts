import { isNilCore } from "./internal";

export function isNotNil<T>(valueLike: T | null | undefined): valueLike is NonNullable<T> {
  return !isNilCore(valueLike);
}
