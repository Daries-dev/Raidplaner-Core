/**
 * User editing capabilities for the character list.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Ajax", "./Action/DeleteAction", "./Action/DisableAction", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Notification", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Ui/Dropdown/Simple", "WoltLabSuite/Core/Controller/Clipboard"], function (require, exports, tslib_1, Handler_1, Ajax_1, DeleteAction_1, DisableAction_1, Language_1, Notification_1, Core_1, Simple_1, Clipboard_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.AcpUiCharacterEditor = void 0;
    DeleteAction_1 = tslib_1.__importDefault(DeleteAction_1);
    DisableAction_1 = tslib_1.__importDefault(DisableAction_1);
    Simple_1 = tslib_1.__importDefault(Simple_1);
    class AcpUiCharacterEditor {
        /**
         * Initializes the edit dropdown for each character.
         */
        constructor() {
            document.querySelectorAll(".jsCharacterRow").forEach((characterRow) => {
                this.#initCharacter(characterRow);
            });
            this.#initHandler();
        }
        async #clipboardAction(actionData) {
            const characterIds = Object.values(actionData.data.parameters.objectIDs);
            if (actionData.data.actionName === "dev.daries.rp.character.enable") {
                await (0, Ajax_1.dboAction)("enable", "rp\\data\\character\\CharacterAction")
                    .objectIds(characterIds)
                    .dispatch();
                Array.from(this.#getCharacterRows(characterIds)).forEach((characterRow) => {
                    characterRow.dataset.enabled = "true";
                    const characterId = ~~characterRow.dataset.objectId;
                    const dropdownId = `characterListDropdown${characterId}`;
                    const dropdownMenu = Simple_1.default.getDropdownMenu(dropdownId);
                    const enableCharacter = dropdownMenu.querySelector(".jsEnable");
                    if (enableCharacter !== null) {
                        enableCharacter.textContent = enableCharacter.dataset.disableMessage;
                    }
                });
                (0, Notification_1.show)();
                this.#refreshCharacters({
                    characterIds: characterIds,
                });
                (0, Clipboard_1.unmark)("dev.daries.rp.character", characterIds);
            }
        }
        #getCharacterRows(characterIds) {
            const rows = [];
            document.querySelectorAll(".jsCharacterRow").forEach((characterRow) => {
                const characterId = ~~characterRow.dataset.objectId;
                if (characterIds.includes(characterId)) {
                    rows.push(characterRow);
                }
            });
            return rows;
        }
        #initCharacter(characterRow) {
            const characterId = ~~characterRow.dataset.objectId;
            const dropdownId = `characterListDropdown${characterId}`;
            const dropdownMenu = Simple_1.default.getDropdownMenu(dropdownId);
            if (dropdownMenu.childElementCount === 0) {
                const toggleButton = characterRow.querySelector(".dropdownToggle");
                toggleButton?.classList.add("disabled");
                return;
            }
            const editLink = dropdownMenu.querySelector(".jsEditLink");
            if (editLink !== null) {
                const toggleButton = characterRow.querySelector(".dropdownToggle");
                toggleButton?.addEventListener("dblclick", (event) => {
                    event.preventDefault();
                    editLink.click();
                });
            }
            const enableCharacter = dropdownMenu.querySelector(".jsEnable");
            if (enableCharacter !== null) {
                new DisableAction_1.default(enableCharacter, characterId, characterRow);
            }
            const deleteCharacter = dropdownMenu.querySelector(".jsDelete");
            if (deleteCharacter !== null) {
                new DeleteAction_1.default(deleteCharacter, characterId, characterRow);
            }
        }
        #initHandler() {
            (0, Handler_1.add)("dev.daries.rp.acp.character", "refresh", (data) => {
                this.#refreshCharacters(data);
            });
            (0, Handler_1.add)("com.woltlab.wcf.clipboard", "dev.daries.rp.character", (data) => {
                void this.#clipboardAction(data);
            });
        }
        #refreshCharacters(data) {
            document.querySelectorAll(".jsCharacterRow").forEach((characterRow) => {
                const characterId = ~~characterRow.dataset.objectId;
                if (data.characterIds.includes(characterId)) {
                    const characterStatusIcons = characterRow.querySelector(".characterStatusIcons");
                    const isDisabled = !(0, Core_1.stringToBool)(characterRow.dataset.enabled);
                    let iconIsDisabled = characterRow.querySelector(".jsCharacterIsDisabled");
                    if (isDisabled && iconIsDisabled === null) {
                        iconIsDisabled = document.createElement("span");
                        iconIsDisabled.innerHTML = '<fa-icon name="power-off"></fa-icon>';
                        iconIsDisabled.classList.add("jsCharacterIsDisabled", "jsTooltip");
                        iconIsDisabled.title = (0, Language_1.getPhrase)("rp.acp.character.isDisabled");
                        characterStatusIcons.appendChild(iconIsDisabled);
                    }
                    else {
                        iconIsDisabled.remove();
                    }
                }
            });
        }
    }
    exports.AcpUiCharacterEditor = AcpUiCharacterEditor;
    exports.default = AcpUiCharacterEditor;
});
