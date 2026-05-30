import { describe, it, expect, vi, beforeAll, afterEach } from "vitest";
import { subscribable, unwrap } from "knockout";

import { DialogQueueService } from "./dialog-queue.service";

const createMockCloseSubscription = () => new subscribable();

describe("DialogQueueServiceSpecs", () => {
  let service: DialogQueueService;

  beforeAll(() => {
    service = DialogQueueService.getInstance();
  });

  afterEach(() => {
    const queue = service.getDialogQueue();
    queue([]);
  });

  it("is a singleton", () => {
    expect(DialogQueueService.getInstance()).toBe(service);
  });

  it("openDialog adds an HTML dialog", () => {
    const closeFn = vi.fn();

    service.openDialog(
      "id-1",
      "title-1",
      "<p>hello</p>",
      [
        { id: "a1", label: "Action", onClick: vi.fn() }
      ] as any,
      createMockCloseSubscription(),
      () => true,
      closeFn
    );

    const items = unwrap(service.getDialogQueue());
    expect(items).toHaveLength(1);

    const dialog = items[0];
    expect(dialog.type).toBe("HTML");
    expect(dialog.messageHtml()).toBe("<p>hello</p>");
    expect(dialog.actions().length).toBe(1);
  });

  it("openDialogWithComponent adds a component dialog", () => {
    service.openDialogWithComponent(
      "id-2",
      "title-2",
      "my-selector",
      { foo: "bar" },
      createMockCloseSubscription()
    );

    const items = unwrap(service.getDialogQueue());
    expect(items).toHaveLength(1);

    const dialog = items[0];
    expect(dialog.type).toBe("COMPONENT");
    expect(dialog.componentSelector()).toBe("my-selector");
    expect(dialog.componentPayload()).toEqual({ foo: "bar" });
  });

  it("onClose shifts queue and calls provided closeFn (HTML dialog)", () => {
    const closeFn = vi.fn();

    service.openDialog(
      "id-3",
      "title-3",
      "<div/>",
      [] as any,
      createMockCloseSubscription(),
      undefined,
      closeFn
    );

    const dialog = unwrap(service.getDialogQueue())[0];

    dialog.onClose();

    expect(closeFn).toHaveBeenCalledTimes(1);
    expect(unwrap(service.getDialogQueue())).toHaveLength(0);
  });

  it("onClose works without closeFn", () => {
    service.openDialog(
      "id-4",
      "title-4",
      "<div/>",
      [] as any,
      createMockCloseSubscription()
    );

    const dialog = unwrap(service.getDialogQueue())[0];

    expect(() => dialog.onClose()).not.toThrow();
    expect(unwrap(service.getDialogQueue())).toHaveLength(0);
  });

  it("onClose works for component dialog", () => {
    const closeFn = vi.fn();

    service.openDialogWithComponent(
      "id-5",
      "title-5",
      "selector",
      { a: 1 },
      createMockCloseSubscription(),
      undefined,
      closeFn
    );

    const dialog = unwrap(service.getDialogQueue())[0];

    dialog.onClose();

    expect(closeFn).toHaveBeenCalledTimes(1);
    expect(unwrap(service.getDialogQueue())).toHaveLength(0);
  });

  it("queue behaves FIFO when multiple dialogs are added and closed", () => {
    const closeFn1 = vi.fn();
    const closeFn2 = vi.fn();

    service.openDialog(
      "first",
      "First",
      "A",
      [] as any,
      createMockCloseSubscription(),
      undefined,
      closeFn1
    );

    service.openDialog(
      "second",
      "Second",
      "B",
      [] as any,
      createMockCloseSubscription(),
      undefined,
      closeFn2
    );

    const queue1 = unwrap(service.getDialogQueue());
    expect(queue1[0].id()).toBe("first");
    expect(queue1[1].id()).toBe("second");

    queue1[0].onClose();

    expect(closeFn1).toHaveBeenCalledTimes(1);

    const queue2 = unwrap(service.getDialogQueue());
    expect(queue2).toHaveLength(1);
    expect(queue2[0].id()).toBe("second");
  });
});
