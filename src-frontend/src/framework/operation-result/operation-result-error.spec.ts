import { describe, it, expect } from "vitest";
import { OperationResultError } from "./operation-result-error";
import { OperationResultBase } from "./operation-result-base";

describe("OperationResultError", () => {
  it("should set isError and isSuccess correctly", () => {
    const errors = ["Something went wrong"];
    const result = new OperationResultError(...errors);

    expect(result["severity"]).toBe("ERROR");
  });

  it("should store errors correctly", () => {
    const errors = ["Error 1", "Error 2"];
    const result = new OperationResultError(...errors);

    expect(result.errors).toEqual(errors);
  });

  it("should work with isErrorTC type guard", () => {
    const result = new OperationResultError("Oops");
    const base: OperationResultBase = result;

    if (base.isErrorTC()) {
      expect(base.errors).toEqual(["Oops"]);
    } else {
      throw new Error("Type guard failed");
    }
  });

  it("should return false for isSuccessTC type guard", () => {
    const result = new OperationResultError("Oops");
    const base: OperationResultBase = result;

    const successCheck = base.isSuccessTC<string>();
    expect(successCheck).toBe(false);
  });
});
