import { deepmerge as _deepmerge } from "deepmerge-ts";

export function deepmerge<T, U>(
  left: T,
  right: U
) {
  _deepmerge(left, right);
}