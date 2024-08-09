/**
 * An abstract action, to handle event actions.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports"], function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.AbstractEventAction = void 0;
    class AbstractEventAction {
        button;
        event;
        eventId;
        constructor(button, event) {
            this.button = button;
            this.event = event;
            this.eventId = parseInt(event.dataset.eventId);
        }
    }
    exports.AbstractEventAction = AbstractEventAction;
    exports.default = AbstractEventAction;
});
