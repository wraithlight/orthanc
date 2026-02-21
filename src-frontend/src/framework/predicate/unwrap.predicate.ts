import { Predicate } from "../type";

export function unwrap<T, U>(
  object: T,
  predicate: Predicate<T, U>
): U {
  return predicate(object);
}
