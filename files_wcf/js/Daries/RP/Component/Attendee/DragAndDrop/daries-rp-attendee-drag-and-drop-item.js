/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "../../../Ui/Event/Raid/Participant/DragAndDrop/Autobind"], function (require, exports, tslib_1, Autobind_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.DariesRPAttendeeDragAndDropItemElement = void 0;
    class DariesRPAttendeeDragAndDropItemElement extends HTMLElement {
        connectedCallback() {
            this.addEventListener("dragstart", (event) => {
                this.dragStartHandler(event);
            });
            this.addEventListener("dragend", (event) => {
                this.dragEndHandler(event);
            });
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
        get attendeeId() {
            return parseInt(this.getAttribute("attendee-id"));
        }
        get droppableTo() {
            return this.getAttribute("droppable-to");
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
