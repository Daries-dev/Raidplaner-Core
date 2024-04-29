/**
 * Deletes a given character.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
define(["require", "exports", "WoltLabSuite/Core/Component/Confirmation", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, Confirmation_1, Ajax_1, Notification_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.Delete = void 0;
    class Delete {
        #characterIds;
        #deleteName;
        #successCallback;
        constructor(characterIds, successCallback, deleteName) {
            this.#characterIds = characterIds;
            this.#successCallback = successCallback;
            this.#deleteName = deleteName;
        }
        async delete() {
            const result = await (0, Confirmation_1.confirmationFactory)().delete(this.#deleteName);
            if (result) {
                await (0, Ajax_1.dboAction)("delete", "rp\\data\\character\\CharacterAction").objectIds(this.#characterIds).dispatch();
                this.#successCallback();
                (0, Notification_1.show)();
            }
        }
    }
    exports.Delete = Delete;
    exports.default = Delete;
});
