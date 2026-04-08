vi.mock('./internal', () => ({ isNilCore: vi.fn() }));

import { vi, describe, it, expect } from 'vitest';

import { isNil } from './is-nil';
import { isNilCore as isNilCoreSpy } from './internal';

describe('isNilSpecs', () => {
  it('must call isNilCore', () => {
    isNil(undefined);
    expect(isNilCoreSpy).toHaveBeenCalled();
    expect(isNilCoreSpy).toHaveBeenCalledTimes(1);
    expect(isNilCoreSpy).toHaveBeenCalledWith(undefined);
  });
});
