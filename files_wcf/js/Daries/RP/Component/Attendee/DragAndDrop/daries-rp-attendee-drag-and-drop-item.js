/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "../../../Ui/Event/Raid/Participant/DragAndDrop/Autobind", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Language", "../../../Api/Attendees/UpdateAttendeeStatus", "WoltLabSuite/Core/Ui/Dropdown/Simple"], function (require, exports, tslib_1, Autobind_1, Dialog_1, Language_1, UpdateAttendeeStatus_1, Simple_1) {
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
        #updateStatus() {
            if (!this.#dialog) {
                this.#dialog = (0, Dialog_1.dialogFactory)().fromHtml(this.#statusDialog).asPrompt();
                const status = this.#dialog.content.querySelector('select[name="status"]');
                this.#dialog.addEventListener("primary", async () => {
                    const response = await (0, UpdateAttendeeStatus_1.updateAttendeeStatus)(this.attendeeId, this.distributionId, status.value);
                    if (!response.ok) {
                        const validationError = response.error.getValidationError();
                        if (validationError === undefined) {
                            throw response.error;
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
