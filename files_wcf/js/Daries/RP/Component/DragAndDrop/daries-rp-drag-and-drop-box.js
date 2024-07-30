/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "../../Ui/Event/Raid/Participant/DragAndDrop/Autobind", "@woltlab/d.ts/WoltLabSuite/Core/Component/Dialog", "../../Api/Attendee/UpdateAttendee", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, Autobind_1, Dialog_1, UpdateAttendee_1, Notification_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.DariesRPDragAndDropBoxElement = void 0;
    class DariesRPDragAndDropBoxElement extends HTMLElement {
        connectedCallback() {
            this.addEventListener("dragover", (event) => {
                this.dragOverHandler(event);
            });
            this.addEventListener("drop", (event) => {
                void this.dropHandler(event);
            });
            this.addEventListener("dragleave", (event) => {
                this.dragLeaveHandler(event);
            });
        }
        dragOverHandler(event) {
            if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move")
                return;
            event.preventDefault();
            const droppable = this.droppable;
            const droppableTo = event.dataTransfer.getData("droppableTo");
            if (!droppableTo.includes(droppable))
                return;
            this.classList.add("selected");
        }
        async dropHandler(event) {
            if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move")
                return;
            event.preventDefault();
            const droppable = this.droppable;
            const droppableTo = event.dataTransfer.getData("droppableTo");
            if (!droppableTo.includes(droppable))
                return;
            const distributionId = this.distributionId;
            const status = this.status;
            if (status === event.dataTransfer.getData("currentStatus") &&
                distributionId === parseInt(event.dataTransfer.getData("distributionID"))) {
                return;
            }
            const attendeeId = parseInt(event.dataTransfer.getData("attendeeID"));
            const response = await (0, UpdateAttendee_1.updateAttendee)(attendeeId, this.distributionId, this.status);
            if (!response.ok) {
                const validationError = response.error.getValidationError();
                if (validationError === undefined) {
                    throw response.error;
                }
                (0, Dialog_1.dialogFactory)().fromHtml(`<p>${validationError.message}</p>`).asAlert();
                return;
            }
            const attendeeList = this.querySelector(".attendeeList");
            const attendee = document.getElementById(event.dataTransfer.getData("id"));
            attendeeList?.insertAdjacentElement("beforeend", attendee);
            (0, Notification_1.show)();
        }
        dragLeaveHandler(event) {
            if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move")
                return;
            event.preventDefault();
            this.classList.remove("selected");
        }
        get distributionId() {
            return parseInt(this.getAttribute("distribution-id"));
        }
        get droppable() {
            return this.getAttribute("droppable");
        }
        get status() {
            return this.getAttribute("status");
        }
    }
    exports.DariesRPDragAndDropBoxElement = DariesRPDragAndDropBoxElement;
    tslib_1.__decorate([
        Autobind_1.Autobind,
        tslib_1.__metadata("design:type", Function),
        tslib_1.__metadata("design:paramtypes", [DragEvent]),
        tslib_1.__metadata("design:returntype", void 0)
    ], DariesRPDragAndDropBoxElement.prototype, "dragOverHandler", null);
    tslib_1.__decorate([
        Autobind_1.Autobind,
        tslib_1.__metadata("design:type", Function),
        tslib_1.__metadata("design:paramtypes", [DragEvent]),
        tslib_1.__metadata("design:returntype", Promise)
    ], DariesRPDragAndDropBoxElement.prototype, "dropHandler", null);
    tslib_1.__decorate([
        Autobind_1.Autobind,
        tslib_1.__metadata("design:type", Function),
        tslib_1.__metadata("design:paramtypes", [DragEvent]),
        tslib_1.__metadata("design:returntype", void 0)
    ], DariesRPDragAndDropBoxElement.prototype, "dragLeaveHandler", null);
    window.customElements.define("daries-rp-drag-and-drop-box", DariesRPDragAndDropBoxElement);
    exports.default = DariesRPDragAndDropBoxElement;
});
