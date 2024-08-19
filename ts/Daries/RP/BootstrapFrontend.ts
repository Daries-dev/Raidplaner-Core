/**
 * Bootstraps RP's JavaScript with additions for the frontend usage.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { whenFirstSeen } from "WoltLabSuite/Core/LazyLoader";

export function setup(options: BootstrapOptions): void {
  window.RP_API_URL = options.RP_API_URL;

  setupCharacterPopover(options.endpointCharacterPopover);

  whenFirstSeen("daries-rp-attendee-drag-and-drop-box", () => {
    void import("./Component/Attendee/DragAndDrop/daries-rp-attendee-drag-and-drop-box");
  });
  whenFirstSeen("daries-rp-attendee-drag-and-drop-item", () => {
    void import("./Component/Attendee/DragAndDrop/daries-rp-attendee-drag-and-drop-item");
  });
}

function setupCharacterPopover(endpoint: string): void {
  if (endpoint === "") {
    return;
  }

  whenFirstSeen(".rpCharacterLink", () => {
    void import("WoltLabSuite/Core/Component/Popover").then(({ setupFor }) => {
      setupFor({
        endpoint,
        identifier: "dev.daries.rp.character",
        selector: ".rpCharacterLink",
      });
    });
  });
}

declare global {
  interface Window {
    RP_API_URL: string;
  }
}

interface BootstrapOptions {
  endpointCharacterPopover: string;
  RP_API_URL: string;
}
