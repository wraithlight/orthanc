import { describe, it, expect } from "vitest";
import { OperationResultWarning } from "./operation-result-warning";
import { OperationResultBase } from "./operation-result-base";

describe("OperationResultWarning", () => {
  it("should set isSuccess and isError correctly", () => {
    const result = new OperationResultWarning("payload");

    expect(result["severity"]).toBe("WARNING");
  });

  it("should store payload correctly", () => {
    const payload = { data: 123 };
    const result = new OperationResultWarning(payload);

    expect(result.payload).toEqual(payload);
  });

  it("should work with isWarnTC type guard", () => {
    const payload = [1, 2, 3];
    const result: OperationResultBase = new OperationResultWarning(payload);

    if (result.isWarnTC()) {
      expect(result.payload).toEqual(payload);
    } else {
      throw new Error("Type guard failed");
    }
  });

  it("should return false for isErrorTC type guard", () => {
    const result: OperationResultBase = new OperationResultWarning("ok");

    const errorCheck = result.isErrorTC();
    expect(errorCheck).toBe(false);
  });

  it("should return false for isSuccessTC type guard", () => {
    const result: OperationResultBase = new OperationResultWarning("ok");

    const successCheck = result.isSuccessTC();
    expect(successCheck).toBe(false);
  });

  it("should allow payload to be undefined when generic is omitted", () => {
    const result = new OperationResultWarning(undefined);
    expect(result.payload).toBeUndefined();
  });
});
