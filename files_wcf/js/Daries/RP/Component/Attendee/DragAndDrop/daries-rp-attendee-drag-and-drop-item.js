/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ui/Dropdown/Simple", "../../../Ui/Event/Raid/Participant/DragAndDrop/Autobind", "../../../Api/Events/AvailableCharacters", "../../../Api/Attendees/CreateAttendee", "WoltLabSuite/Core/Component/Dialog", "../../../Api/Attendees/DeleteAttendee", "WoltLabSuite/Core/Language", "../../../Api/Attendees/RenderAttendee", "WoltLabSuite/Core/Ui/Notification", "../../../Api/Attendees/UpdateAttendeeStatus"], function (require, exports, tslib_1, Simple_1, Autobind_1, AvailableCharacters_1, CreateAttendee_1, Dialog_1, DeleteAttendee_1, Language_1, RenderAttendee_1, Notification_1, UpdateAttendeeStatus_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.DariesRPAttendeeDragAndDropItemElement = void 0;
    Simple_1 = tslib_1.__importDefault(Simple_1);
    class DariesRPAttendeeDragAndDropItemElement extends HTMLElement {
        #dialog;
        #statusDialog;
        connectedCallback() {
            this.addEventListener("dragstart", (event) => {
                this.dragStartHandler(event);
            });
            this.addEventListener("dragend", (event) => {
                this.dragEndHandler(event);
            });
            if (this.menu) {
                this.#statusDialog = `
        <div class="section">
            <dl>
                <dt>${(0, Language_1.getPhrase)("rp.event.raid.status")}</dt>
                <dd>
                    <select name="status">
                        <option value="0">${(0, Language_1.getPhrase)("rp.event.raid.container.login")}</option>
                        <option value="3">${(0, Language_1.getPhrase)("rp.event.raid.container.reserve")}</option>
                        <option value="2">${(0, Language_1.getPhrase)("rp.event.raid.container.logout")}</option>
                    </select>
                </dd>
            </dl>
        </div>
        `;
                const updateStatusButton = this.menu.querySelector(".attendee__option--update-status");
                updateStatusButton?.addEventListener("click", (event) => {
                    event.preventDefault();
                    this.#updateStatus();
                });
                const switchCharacterButton = this.menu.querySelector(".attendee__option--character-switch");
                switchCharacterButton?.addEventListener("click", (event) => {
                    event.preventDefault();
                    void this.#switchCharacter();
                });
            }
        }
        dragEndHandler(_) {
            document.querySelectorAll(".attendeeBox").forEach((attendeeBox) => {
                attendeeBox.classList.remove("droppable");
                attendeeBox.classList.remove("selected");
            });
        }
        dragStartHandler(event) {
            event.dataTransfer.setData("id", this.id);
            event.dataTransfer.setData("attendeeId", this.attendeeId.toString());
            event.dataTransfer.setData("droppableTo", this.droppableTo);
            event.dataTransfer.effectAllowed = "move";
            const currentBox = this.closest(".attendeeBox");
            event.dataTransfer.setData("currentStatus", currentBox.getAttribute("status"));
            event.dataTransfer.setData("distributionId", currentBox.getAttribute("distribution-id"));
            document.querySelectorAll(".attendeeBox").forEach((attendeeBox) => {
                const droppable = attendeeBox.getAttribute("droppable");
                const droppableTo = this.droppableTo;
                if (!droppableTo.includes(droppable))
                    return;
                attendeeBox.classList.add("droppable");
            });
        }
        async #loadSwitchCharacter(attendeeId) {
            const response = await (0, RenderAttendee_1.renderAttendee)(attendeeId);
            if (!response.ok) {
                const validationError = response.error.getValidationError();
                if (validationError === undefined) {
                    throw new Error("Unexpected validation error", { cause: response.error });
                }
                this.remove();
                return;
            }
            const box = document.querySelector(`daries-rp-attendee-drag-and-drop-box[distribution-id="${response.value.distributionId}"][status="${this.status}"]`);
            const attendeeList = box?.querySelector(".attendeeList");
            attendeeList?.insertAdjacentHTML("beforeend", response.value.template);
            (0, Notification_1.show)();
            this.remove();
        }
        async #switchCharacter() {
            const { template } = (await (0, AvailableCharacters_1.availableCharacters)(this.eventId)).unwrap();
            console.log(template);
            this.#showSwitchDialog(template);
        }
        #showSwitchDialog(template) {
            const dialog = (0, Dialog_1.dialogFactory)().fromHtml(template).asPrompt();
            const characterId = dialog.content.querySelector('select[name="characterID"]');
            const roleId = dialog.content.querySelector('select[name="roleId"]');
            dialog.addEventListener("primary", async () => {
                (await (0, DeleteAttendee_1.deleteAttendee)(this.attendeeId)).unwrap();
                this.dispatchEvent(new CustomEvent("delete"));
                const response = await (0, CreateAttendee_1.createAttendee)(this.eventId, characterId.value, parseInt(roleId.value), this.status);
                if (!response.ok) {
                    const validationError = response.error.getValidationError();
                    if (validationError === undefined) {
                        throw new Error("Unexpected validation error", { cause: response.error });
                    }
                    this.remove();
                    return;
                }
                void this.#loadSwitchCharacter(response.value.attendeeId);
            });
            dialog.show((0, Language_1.getPhrase)("rp.character.selection"));
        }
        #updateStatus() {
            if (!this.#dialog) {
                this.#dialog = (0, Dialog_1.dialogFactory)().fromHtml(this.#statusDialog).asPrompt();
                const status = this.#dialog.content.querySelector('select[name="status"]');
                this.#dialog.addEventListener("primary", async () => {
                    const response = await (0, UpdateAttendeeStatus_1.updateAttendeeStatus)(this.attendeeId, this.distributionId, status.value);
                    if (!response.ok) {
                        const validationError = response.error.getValidationError();
                        if (validationError === undefined) {
                            throw new Error("Unexpected validation error", { cause: response.error });
                        }
                        (0, Dialog_1.dialogFactory)().fromHtml(`<p>${validationError.message}</p>`).asAlert();
                        return;
                    }
                    const dragAndDropBox = document.querySelector(`daries-rp-attendee-drag-and-drop-box[status="${status.value}"][distribution-id="${this.distributionId}"]`);
                    const attendeeList = dragAndDropBox?.querySelector(".attendeeList");
                    attendeeList?.insertAdjacentElement("beforeend", this);
                });
            }
            this.#dialog.show((0, Language_1.getPhrase)("rp.event.raid.updateStatus"));
        }
        get attendeeId() {
            return parseInt(this.getAttribute("attendee-id"));
        }
        get box() {
            return this.closest("daries-rp-attendee-drag-and-drop-box");
        }
        get distributionId() {
            return parseInt(this.getAttribute("distribution-id"));
        }
        get droppableTo() {
            return this.getAttribute("droppable-to");
        }
        get eventId() {
            return parseInt(this.getAttribute("event-id"));
        }
        get menu() {
            let menu = Simple_1.default.getDropdownMenu(`attendeeOptions${this.attendeeId}`);
            if (menu === undefined) {
                menu = this.querySelector(".attendee__menu .dropdownMenu") || undefined;
            }
            return menu;
        }
        get status() {
            return parseInt(this.box.getAttribute("status"));
        }
    }
    exports.DariesRPAttendeeDragAndDropItemElement = DariesRPAttendeeDragAndDropItemElement;
    tslib_1.__decorate([
        Autobind_1.Autobind,
        tslib_1.__metadata("design:type", Function),
        tslib_1.__metadata("design:paramtypes", [DragEvent]),
        tslib_1.__metadata("design:returntype", void 0)
    ], DariesRPAttendeeDragAndDropItemElement.prototype, "dragEndHandler", null);
    tslib_1.__decorate([
        Autobind_1.Autobind,
        tslib_1.__metadata("design:type", Function),
        tslib_1.__metadata("design:paramtypes", [DragEvent]),
        tslib_1.__metadata("design:returntype", void 0)
    ], DariesRPAttendeeDragAndDropItemElement.prototype, "dragStartHandler", null);
    window.customElements.define("daries-rp-attendee-drag-and-drop-item", DariesRPAttendeeDragAndDropItemElement);
    exports.default = DariesRPAttendeeDragAndDropItemElement;
});
