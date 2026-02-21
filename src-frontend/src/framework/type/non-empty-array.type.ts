export type NonEmptyArray<T> = Array<T> & [T, ...Array<T>];
