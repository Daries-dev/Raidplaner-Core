/**
 * Bootstraps RP's JavaScript with additions for the frontend usage.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */

import { whenFirstSeen } from "WoltLabSuite/Core/LazyLoader";

export function setup(): void {
    whenFirstSeen("daries-rp-drag-and-drop-box", () => {
        void import("./Component/DragAndDrop/daries-rp-drag-and-drop-box");
    });
}