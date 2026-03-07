import { describe, it, expect } from "vitest";

import { OperationResultFactory } from "./operation-result.factory";
import { OperationResultSuccess } from "./operation-result-success";
import { OperationResultError } from "./operation-result-error";

describe("OperationResultFactory", () => {
  it("should create a success result with payload", () => {
    const payload = { foo: "bar" };
    const result = OperationResultFactory.success(payload);

    expect(result).toBeInstanceOf(OperationResultSuccess);
    expect(result.isSuccess).toBe(true);
    expect(result.isError).toBe(false);
    expect(result.payload).toEqual(payload);
  });

  it("should create a success result with undefined payload by default", () => {
    const result = OperationResultFactory.success(undefined);

    expect(result).toBeInstanceOf(OperationResultSuccess);
    expect(result.payload).toBeUndefined();
  });

  it("should create an error result with single error", () => {
    const errorMessage = "Something went wrong";
    const result = OperationResultFactory.error(errorMessage);

    expect(result).toBeInstanceOf(OperationResultError);
    expect(result.isError).toBe(true);
    expect(result.isSuccess).toBe(false);
    expect(result.errors).toEqual([errorMessage]);
  });

  it("should create an error result with multiple errors", () => {
    const errors = ["Error 1", "Error 2"];
    const result = OperationResultFactory.error(...errors);

    expect(result.errors).toEqual(errors);
  });
});
