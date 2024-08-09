/**
 * Restore a event.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
define(["require", "exports", "WoltLabSuite/Core/Ajax/Backend", "WoltLabSuite/Core/Api/Result"], function (require, exports, Backend_1, Result_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.restoreEvent = void 0;
    async function restoreEvent(eventId) {
        try {
            await (0, Backend_1.prepareRequest)(`${window.WSC_API_URL}index.php?api/rpc/rp/events/${eventId}/restore`).post().fetchAsJson();
        }
        catch (e) {
            return (0, Result_1.apiResultFromError)(e);
        }
        return (0, Result_1.apiResultFromValue)([]);
    }
    exports.restoreEvent = restoreEvent;
});
