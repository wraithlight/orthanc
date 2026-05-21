import { describe, it, expect } from 'vitest';

import { isNilCore } from './is-nil';

describe('isNilCore', () => {
  it('returns true for null', () => {
    expect(isNilCore(null)).toBe(true);
  });

  it('returns true for undefined', () => {
    expect(isNilCore(undefined)).toBe(true);
  });

  it('returns false for numbers', () => {
    expect(isNilCore(0)).toBe(false);
    expect(isNilCore(42)).toBe(false);
  });

  it('returns false for strings', () => {
    expect(isNilCore('')).toBe(false);
    expect(isNilCore('hello')).toBe(false);
  });

  it('returns false for objects', () => {
    expect(isNilCore({})).toBe(false);
    expect(isNilCore({ a: 1 })).toBe(false);
  });

  it('returns false for arrays', () => {
    expect(isNilCore([])).toBe(false);
    expect(isNilCore([1, 2, 3])).toBe(false);
  });

  it('returns false for boolean values', () => {
    expect(isNilCore(true)).toBe(false);
    expect(isNilCore(false)).toBe(false);
  });

  it('returns false for functions', () => {
    expect(isNilCore(() => {})).toBe(false);
  });
});
