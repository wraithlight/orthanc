vi.mock('./internal', () => ({ isNilCore: vi.fn() }));

import { vi, describe, it, expect } from 'vitest';

import { isNotNil } from './is-not-nil';
import { isNilCore as isNilCoreSpy } from './internal';

describe('isNotNilSpecs', () => {
  it('must call isNilCore', () => {
    isNotNil(undefined);
    expect(isNilCoreSpy).toHaveBeenCalled();
    expect(isNilCoreSpy).toHaveBeenCalledTimes(1);
    expect(isNilCoreSpy).toHaveBeenCalledWith(undefined);
  });
});
