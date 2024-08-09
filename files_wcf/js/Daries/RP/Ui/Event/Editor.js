/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ui/Dropdown/Simple", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Component/Confirmation", "WoltLabSuite/Core/Ui/Notification", "../../Api/Events/TrashEvent", "WoltLabSuite/Core/Component/Dialog", "../../Api/Events/RestoreEvent", "../../Api/Events/EnableDisableEvent"], function (require, exports, tslib_1, Simple_1, Core_1, Language_1, Confirmation_1, Notification_1, TrashEvent_1, Dialog_1, RestoreEvent_1, EnableDisableEvent_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.UiEventEditor = void 0;
    Simple_1 = tslib_1.__importDefault(Simple_1);
    class UiEventEditor {
        #event;
        #eventIcons;
        #eventId;
        #restoreButton = null;
        #trashButton = null;
        constructor() {
            this.#event = document.querySelector(".event");
            if (!(0, Core_1.stringToBool)(this.#event.dataset.canEdit))
                return;
            this.#eventId = parseInt(this.#event.dataset.eventId);
            this.#eventIcons = document.querySelector(".rpEventHeader .contentHeaderTitle .contentTitle");
            this.#initEvent();
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
            this.#initEnableButton(dropdownMenu);
            this.#initRestoreButton(dropdownMenu);
            this.#initTrashButton(dropdownMenu);
        }
        #initEnableButton(dropdownMenu) {
            const enableEvent = dropdownMenu.querySelector(".jsEnable");
            if (enableEvent) {
                enableEvent.addEventListener("click", async () => {
                    const isEnabled = (0, Core_1.stringToBool)(this.#event.dataset.enabled);
                    const response = await (0, EnableDisableEvent_1.enableDisableEvent)(this.#eventId, isEnabled);
                    if (!response.ok) {
                        const validationError = response.error.getValidationError();
                        if (validationError === undefined) {
                            throw response.error;
                        }
                        (0, Dialog_1.dialogFactory)().fromHtml(`<p>${validationError.message}</p>`).asAlert();
                        return;
                    }
                    this.#event.dataset.enabled = isEnabled ? "false" : "true";
                    if (isEnabled) {
                        enableEvent.textContent = enableEvent.dataset.enableMessage;
                    }
                    else {
                        enableEvent.textContent = enableEvent.dataset.disableMessage;
                    }
                    const isDisabled = !(0, Core_1.stringToBool)(this.#event.dataset.enabled);
                    let iconIsDisabled = document.querySelector(".rpEventHeader .jsIsDisabled");
                    if (isDisabled && iconIsDisabled === null) {
                        iconIsDisabled = document.createElement("span");
                        iconIsDisabled.classList.add("badge", "label", "green", "jsIsDisabled");
                        iconIsDisabled.innerHTML = (0, Language_1.getPhrase)("wcf.message.status.disabled");
                        this.#eventIcons.appendChild(iconIsDisabled);
                    }
                    else if (!isDisabled && iconIsDisabled !== null) {
                        iconIsDisabled.remove();
                    }
                    (0, Notification_1.show)();
                });
            }
        }
        #initRestoreButton(dropdownMenu) {
            if ((0, Core_1.stringToBool)(this.#event.dataset.canRestore)) {
                this.#restoreButton = dropdownMenu.querySelector(".jsRestore");
                if (this.#restoreButton) {
                    this.#restoreButton.addEventListener("click", async () => {
                        const title = this.#event.dataset.title;
                        const result = await (0, Confirmation_1.confirmationFactory)().restore(title);
                        if (result) {
                            const response = await (0, RestoreEvent_1.restoreEvent)(this.#eventId);
                            if (!response.ok) {
                                const validationError = response.error.getValidationError();
                                if (validationError === undefined) {
                                    throw response.error;
                                }
                                (0, Dialog_1.dialogFactory)().fromHtml(`<p>${validationError.message}</p>`).asAlert();
                                return;
                            }
                            const iconIsDeleted = document.querySelector(".rpEventHeader .jsIsDeleted");
                            if (iconIsDeleted !== null) {
                                iconIsDeleted.remove();
                            }
                            this.#event.dataset.deleted = "false";
                            this.#restoreButton.parentElement.hidden = true;
                            this.#trashButton.parentElement.hidden = false;
                            (0, Notification_1.show)();
                        }
                    });
                    if ((0, Core_1.stringToBool)(this.#event.dataset.deleted)) {
                        this.#restoreButton.parentElement.hidden = false;
                    }
                }
            }
        }
        #initTrashButton(dropdownMenu) {
            if ((0, Core_1.stringToBool)(this.#event.dataset.canDelete)) {
                this.#trashButton = dropdownMenu.querySelector(".jsTrash");
                if (this.#trashButton) {
                    this.#trashButton.addEventListener("click", async () => {
                        const title = this.#event.dataset.title;
                        const { result } = await (0, Confirmation_1.confirmationFactory)().softDelete(title);
                        if (result) {
                            const response = await (0, TrashEvent_1.trashEvent)(this.#eventId);
                            if (!response.ok) {
                                const validationError = response.error.getValidationError();
                                if (validationError === undefined) {
                                    throw response.error;
                                }
                                (0, Dialog_1.dialogFactory)().fromHtml(`<p>${validationError.message}</p>`).asAlert();
                                return;
                            }
                            this.#event.dataset.deleted = "true";
                            let iconIsDeleted = document.querySelector(".rpEventHeader .jsIsDeleted");
                            if (iconIsDeleted === null) {
                                iconIsDeleted = document.createElement("span");
                                iconIsDeleted.classList.add("badge", "label", "red", "jsIsDeleted");
                                iconIsDeleted.innerHTML = (0, Language_1.getPhrase)("wcf.message.status.deleted");
                                this.#eventIcons.appendChild(iconIsDeleted);
                            }
                            this.#restoreButton.parentElement.hidden = false;
                            this.#trashButton.parentElement.hidden = true;
                            (0, Notification_1.show)();
                        }
                    });
                    if (!(0, Core_1.stringToBool)(this.#event.dataset.deleted)) {
                        this.#trashButton.parentElement.hidden = false;
                    }
                }
            }
        }
    }
    exports.UiEventEditor = UiEventEditor;
    exports.default = UiEventEditor;
});
