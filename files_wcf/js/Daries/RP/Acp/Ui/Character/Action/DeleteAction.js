/**
 * An abstract action, to handle character actions.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "./Abstract", "./Handler/Delete"], function (require, exports, tslib_1, Abstract_1, Delete_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.DeleteAction = void 0;
    Abstract_1 = tslib_1.__importDefault(Abstract_1);
    Delete_1 = tslib_1.__importDefault(Delete_1);
    class DeleteAction extends Abstract_1.default {
        executeAction() {
            if (typeof this.button.dataset.characterName !== "string") {
                throw new Error("The button does not provide a characterName.");
            }
            this.button.addEventListener("click", (event) => {
                event.preventDefault();
                const deleteHandler = new Delete_1.default([this.characterId], () => {
                    this.characterDataElement.remove();
                }, this.button.dataset.characterName);
                void deleteHandler.delete();
            });
        }
    }
    exports.DeleteAction = DeleteAction;
    exports.default = DeleteAction;
});
