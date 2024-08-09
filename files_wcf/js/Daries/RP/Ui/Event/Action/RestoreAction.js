/**
 * Handles a event restore button.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Component/Confirmation", "./Abstract", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Ui/Notification", "WoltLabSuite/Core/Event/Handler"], function (require, exports, tslib_1, Confirmation_1, Abstract_1, Ajax_1, Notification_1, Handler_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.RestoreAction = void 0;
    Abstract_1 = tslib_1.__importDefault(Abstract_1);
    class RestoreAction extends Abstract_1.default {
        constructor(button, event) {
            super(button, event);
            this.button.addEventListener("click", (event) => void this.#click(event));
        }
        /**
         * Handles the click event for the button.
         */
        async #click(event) {
            event.preventDefault();
            const title = this.event.dataset.title;
            const result = await (0, Confirmation_1.confirmationFactory)().restore(title);
            if (result) {
                await (0, Ajax_1.dboAction)("restore", "rp\\data\\event\\EventAction").objectIds([this.eventId]).dispatch();
                this.event.dataset.deleted = "false";
                (0, Notification_1.show)();
                (0, Handler_1.fire)("dev.daries.rp.event", "refresh", {
                    action: "restore",
                    eventId: this.eventId,
                });
            }
        }
    }
    exports.RestoreAction = RestoreAction;
});
