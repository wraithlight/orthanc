import { deepmerge as _deepmerge } from "deepmerge-ts";

export function deepmerge<T>(
  left: T,
  right: T
) {
  _deepmerge(left, right);
}