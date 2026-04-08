import { describe, it, expect } from "vitest";

import { OperationResultFactory } from "./operation-result.factory";
import { OperationResultError } from "./operation-result-error";
import { OperationResultSuccess } from "./operation-result-success";
import { OperationResultWarning } from "./operation-result-warning";

describe("OperationResultFactory", () => {
  it("should create a success result with payload", () => {
    const payload = { foo: "bar" };
    const result = OperationResultFactory.success(payload);

    expect(result).toBeInstanceOf(OperationResultSuccess);
    expect(result["severity"]).toBe("SUCCESS");
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
    expect(result["severity"]).toBe("ERROR");
    expect(result.errors).toEqual([errorMessage]);
  });

  it("should create an error result with multiple errors", () => {
    const errors = ["Error 1", "Error 2"];
    const result = OperationResultFactory.error(...errors);

    expect(result.errors).toEqual(errors);
  });

    it("should create a warning result with single error", () => {
    const errorMessage = "Something went wrong";
    const result = OperationResultFactory.warning("", errorMessage);

    expect(result).toBeInstanceOf(OperationResultWarning);
    expect(result["severity"]).toBe("WARNING");
    expect(result.errors).toEqual([errorMessage]);
  });

  it("should create a warning result with multiple errors", () => {
    const errors = ["Error 1", "Error 2"];
    const result = OperationResultFactory.warning("", ...errors);

    expect(result.errors).toEqual(errors);
  });

});
