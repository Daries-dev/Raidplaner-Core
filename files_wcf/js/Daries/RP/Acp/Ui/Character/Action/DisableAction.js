/**
 * An abstract action, to handle character actions.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "./Abstract", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Ui/Notification", "WoltLabSuite/Core/Core"], function (require, exports, tslib_1, Abstract_1, Ajax_1, Handler_1, Notification_1, Core_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.DisableAction = void 0;
    Abstract_1 = tslib_1.__importDefault(Abstract_1);
    class DisableAction extends Abstract_1.default {
        executeAction() {
            this.button.addEventListener("click", (event) => void this.#click(event));
        }
        async #click(event) {
            event.preventDefault();
            const isEnabled = (0, Core_1.stringToBool)(this.characterDataElement.dataset.enabled);
            await (0, Ajax_1.dboAction)(isEnabled ? "disable" : "enable", "rp\\data\\character\\CharacterAction")
                .objectIds([this.characterId])
                .dispatch();
            this.characterDataElement.dataset.enabled = isEnabled ? "false" : "true";
            if (isEnabled) {
                this.button.textContent = this.button.dataset.enableMessage;
            }
            else {
                this.button.textContent = this.button.dataset.disableMessage;
            }
            (0, Notification_1.show)();
            (0, Handler_1.fire)("dev.daries.rp.acp.character", "refresh", {
                characterIds: [this.characterId],
            });
        }
    }
    exports.DisableAction = DisableAction;
    exports.default = DisableAction;
});
