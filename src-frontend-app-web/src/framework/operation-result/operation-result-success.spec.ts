import { describe, it, expect } from "vitest";
import { OperationResultSuccess } from "./operation-result-success";
import { OperationResultBase } from "./operation-result-base";

describe("OperationResultSuccess", () => {
  it("should set isSuccess and isError correctly", () => {
    const result = new OperationResultSuccess("payload");

    expect(result["severity"]).toBe("SUCCESS");
  });

  it("should store payload correctly", () => {
    const payload = { data: 123 };
    const result = new OperationResultSuccess(payload);

    expect(result.payload).toEqual(payload);
  });

  it("should work with isSuccessTC type guard", () => {
    const payload = [1, 2, 3];
    const result: OperationResultBase = new OperationResultSuccess(payload);

    if (result.isSuccessTC<number[]>()) {
      expect(result.payload).toEqual(payload);
    } else {
      throw new Error("Type guard failed");
    }
  });

  it("should return false for isErrorTC type guard", () => {
    const result: OperationResultBase = new OperationResultSuccess("ok");

    const errorCheck = result.isErrorTC();
    expect(errorCheck).toBe(false);
  });

  it("should allow payload to be undefined when generic is omitted", () => {
    const result = new OperationResultSuccess(undefined);
    expect(result.payload).toBeUndefined();
  });
});
