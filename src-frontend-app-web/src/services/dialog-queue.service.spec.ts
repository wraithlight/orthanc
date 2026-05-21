import { describe, it, expect, vi, beforeEach } from "vitest";

import { DialogQueueService } from "./dialog-queue.service";

describe("DialogQueueService", () => {
  let service: DialogQueueService;

  beforeEach(() => {
    service = DialogQueueService.getInstance();
    service.getDialogQueue()().length = 0;
  });

  it("is a singleton", () => {
    expect(DialogQueueService.getInstance()).toBe(service);
  });

  it("opens dialog and stores data", () => {
    const onClick = vi.fn();

    service.openDialog(
      "id",
      "title",
      "<b>msg</b>",
      [{ id: "a", label: "A", onClick }],
      {} as any
    );

    const queue = service.getDialogQueue()();
    expect(queue.length).toBe(1);

    const d = queue[0];
    expect(d.id()).toBe("id");
    expect(d.title()).toBe("title");
    expect(d.messageHtml()).toBe("<b>msg</b>");
    expect(d.actions().length).toBe(1);
  });

  it("calls onOpen if provided", () => {
    const onOpen = vi.fn(() => true);

    service.openDialog(
      "id",
      "t",
      "m",
      [{ id: "a", label: "A", onClick: vi.fn() }],
      {} as any,
      onOpen
    );

    const d = service.getDialogQueue()()[0];
    expect(d.onOpen()).toBe(true);
    expect(onOpen).toHaveBeenCalled();
  });

  it("closes dialog and calls onClose", () => {
    const onClose = vi.fn();

    service.openDialog(
      "id",
      "t",
      "m",
      [{ id: "a", label: "A", onClick: vi.fn() }],
      {} as any,
      undefined,
      onClose
    );

    const queue = service.getDialogQueue();
    const d = queue()[0];

    d.onClose();

    expect(queue().length).toBe(0);
    expect(onClose).toHaveBeenCalled();
  });

  it("closes dialog without onClose safely", () => {
    service.openDialog(
      "id",
      "t",
      "m",
      [{ id: "a", label: "A", onClick: vi.fn() }],
      {} as any
    );

    const queue = service.getDialogQueue();
    queue()[0].onClose();

    expect(queue().length).toBe(0);
  });
});