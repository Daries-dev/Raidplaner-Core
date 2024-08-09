/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ui/Dropdown/Simple", "WoltLabSuite/Core/Event/Handler", "./Action/DisableAction", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Language", "./Action/TrashAction"], function (require, exports, tslib_1, Simple_1, Handler_1, DisableAction_1, Core_1, Language_1, TrashAction_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.UiEventEditor = void 0;
    Simple_1 = tslib_1.__importDefault(Simple_1);
    class UiEventEditor {
        #event;
        #eventId;
        #trashButton = null;
        constructor() {
            this.#event = document.querySelector(".event");
            if (!(0, Core_1.stringToBool)(this.#event.dataset.canEdit))
                return;
            this.#eventId = parseInt(this.#event.dataset.eventId);
            this.#initEvent();
            (0, Handler_1.add)("dev.daries.rp.event", "refresh", (data) => this.#refreshEvent(data));
        }
        #initEvent() {
            const dropdownMenu = Simple_1.default.getDropdownMenu("eventDropdown");
            const editLink = dropdownMenu.querySelector(".jsEditLink");
            if (editLink) {
                const toggleButton = document.querySelector(".eventDropdown > .dropdownToggle");
                toggleButton?.addEventListener("dblclick", (event) => {
                    event.preventDefault();
                    editLink.click();
                });
            }
            const enableEvent = dropdownMenu.querySelector(".jsEnable");
            if (enableEvent) {
                new DisableAction_1.DisableAction(enableEvent, this.#event);
            }
            if ((0, Core_1.stringToBool)(this.#event.dataset.canDelete)) {
                this.#trashButton = dropdownMenu.querySelector(".jsTrash");
                if (this.#trashButton) {
                    new TrashAction_1.TrashAction(this.#trashButton, this.#event);
                    if (!(0, Core_1.stringToBool)(this.#event.dataset.deleted)) {
                        this.#trashButton.parentElement.hidden = false;
                    }
                }
            }
        }
        #refreshEvent(data) {
            if (this.#eventId != data.eventId)
                return;
            const eventIcons = document.querySelector(".rpEventHeader .contentHeaderTitle .contentTitle");
            if (data.action === "disabled") {
                const isDisabled = !(0, Core_1.stringToBool)(this.#event.dataset.enabled);
                let iconIsDisabled = document.querySelector(".rpEventHeader .jsIsDisabled");
                if (isDisabled && iconIsDisabled === null) {
                    iconIsDisabled = document.createElement("span");
                    iconIsDisabled.classList.add("badge", "label", "green", "jsIsDisabled");
                    iconIsDisabled.innerHTML = (0, Language_1.getPhrase)("wcf.message.status.disabled");
                    eventIcons?.appendChild(iconIsDisabled);
                }
                else if (!isDisabled && iconIsDisabled !== null) {
                    iconIsDisabled.remove();
                }
            }
            else if (data.action == "trash") {
                let iconIsDeleted = document.querySelector(".rpEventHeader .jsIsDeleted");
                if (iconIsDeleted === null) {
                    iconIsDeleted = document.createElement("span");
                    iconIsDeleted.classList.add("badge", "label", "green", "jsIsDeleted");
                    iconIsDeleted.innerHTML = (0, Language_1.getPhrase)("wcf.message.status.deleted");
                    eventIcons?.appendChild(iconIsDeleted);
                }
            }
        }
    }
    exports.UiEventEditor = UiEventEditor;
    exports.default = UiEventEditor;
});
