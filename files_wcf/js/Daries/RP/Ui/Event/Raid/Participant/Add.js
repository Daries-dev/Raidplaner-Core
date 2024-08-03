/**
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "WoltLabSuite/Core/Component/Dialog", "WoltLabSuite/Core/Helper/PromiseMutex", "WoltLabSuite/Core/Ui/Notification", "../../../../Api/Attendees/RenderAttendee"], function (require, exports, Dialog_1, PromiseMutex_1, Notification_1, RenderAttendee_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = void 0;
    async function addParticipant(button) {
        const { ok, result } = await (0, Dialog_1.dialogFactory)().usingFormBuilder().fromEndpoint(button.dataset.addParticipant);
        if (ok) {
            const response = await (0, RenderAttendee_1.renderAttendee)(result.attendeeId);
            if (!response.ok) {
                const validationError = response.error.getValidationError();
                if (validationError === undefined) {
                    throw response.error;
                }
                return;
            }
            const box = document.querySelector(`daries-rp-attendee-drag-and-drop-box[distribution-id="${response.value.distributionId}"][status="${result.status}"]`);
            const attendeeList = box?.querySelector(".attendeeList");
            attendeeList?.insertAdjacentHTML("beforeend", response.value.template);
            button.hidden = true;
            (0, Notification_1.show)();
        }
    }
    function setup(button) {
        button.addEventListener("click", (0, PromiseMutex_1.promiseMutex)(() => addParticipant(button)));
    }
    exports.setup = setup;
});
