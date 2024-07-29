/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Helper/PromiseMutex", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, Util_1, Dialog_1, PromiseMutex_1, Notification_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = void 0;
    Util_1 = tslib_1.__importDefault(Util_1);
    async function addParticipant(button) {
        const { ok, result } = await (0, Dialog_1.dialogFactory)().usingFormBuilder().fromEndpoint(button.dataset.addParticipant);
        if (ok) {
            document.querySelectorAll(".attendeeBox").forEach((attendeeBox) => {
                if (result.distributionId === ~~attendeeBox.dataset.objectId &&
                    result.status === ~~attendeeBox.dataset.status) {
                    const attendeeList = attendeeBox.querySelector(".attendeeList");
                    Util_1.default.insertHtml(result.template, attendeeList, "append");
                    Util_1.default.hide(button);
                    (0, Notification_1.show)();
                }
            });
        }
    }
    function setup(button) {
        button.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => addParticipant(button)));
    }
    exports.setup = setup;
});
