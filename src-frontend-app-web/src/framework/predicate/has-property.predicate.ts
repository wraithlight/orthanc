export function hasProperty<
  T = unknown,
  K extends PropertyKey = PropertyKey
>(
  object: T,
  property: K
): object is T & Record<K, unknown> {
  return (
    typeof object === "object" &&
    object !== null &&
    property in object
  );
}
