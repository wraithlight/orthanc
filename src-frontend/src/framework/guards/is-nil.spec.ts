import { vi, describe, it, expect } from 'vitest';

import { isNil } from './is-nil';

const isNilCoreSpy = vi.fn();
vi.mock('./internal', () => ({ isNilCore: isNilCoreSpy }));

describe('isNilSpecs', () => {
  it('must call isNilCore', () => {
    isNil(undefined);
    expect(isNilCoreSpy).toHaveBeenCalled();
    expect(isNilCoreSpy).toHaveBeenCalledTimes(1);
    expect(isNilCoreSpy).toHaveBeenCalledWith(undefined);
  });
});
