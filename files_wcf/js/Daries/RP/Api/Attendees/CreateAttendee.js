/**
 * Create a new attendee.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "WoltLabSuite/Core/Ajax/Backend", "WoltLabSuite/Core/Api/Result"], function (require, exports, Backend_1, Result_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.createAttendee = void 0;
    async function createAttendee(eventId, characterId, roleId, status, guestToken = "") {
        const url = new URL(`${window.WSC_API_URL}index.php?api/rpc/rp/attendees`);
        const payload = {
            eventID: eventId,
            characterID: characterId,
            roleID: roleId,
            status,
            guestToken,
        };
        let response;
        try {
            response = (await (0, Backend_1.prepareRequest)(url).post(payload).fetchAsJson());
        }
        catch (e) {
            return (0, Result_1.apiResultFromError)(e);
        }
        return (0, Result_1.apiResultFromValue)(response);
    }
    exports.createAttendee = createAttendee;
});
