import { describe, it, expect, vi } from "vitest";

import * as framework from "../../framework";
import { IBaseResponse } from "../model";

import { isBaseResponse } from "./is-base-response.guard";

describe("isBaseResponse", () => {
  it("returns true if object has correlationId and payload", () => {
    const obj = { correlationId: "123", payload: {} } as IBaseResponse;

    const spy = vi.spyOn(framework, "hasProperty");
    spy.mockImplementation((o: any, prop) => prop in o);

    expect(isBaseResponse(obj)).toBe(true);

    spy.mockRestore();
  });

  it("returns false if object is missing correlationId", () => {
    const obj = { payload: {} } as unknown;

    const spy = vi.spyOn(framework, "hasProperty");
    spy.mockImplementation((o: any, prop) => prop in o);

    expect(isBaseResponse(obj)).toBe(false);

    spy.mockRestore();
  });

  it("returns false if object is missing payload", () => {
    const obj = { correlationId: "123" } as unknown;

    const spy = vi.spyOn(framework, "hasProperty");
    spy.mockImplementation((o: any, prop) => prop in o);

    expect(isBaseResponse(obj)).toBe(false);

    spy.mockRestore();
  });

  it("returns false if object is null", () => {
    const spy = vi.spyOn(framework, "hasProperty");
    spy.mockImplementation(() => false);

    expect(isBaseResponse(null)).toBe(false);

    spy.mockRestore();
  });

  it("returns false if object is undefined", () => {
    const spy = vi.spyOn(framework, "hasProperty");
    spy.mockImplementation(() => false);

    expect(isBaseResponse(undefined)).toBe(false);

    spy.mockRestore();
  });
});
