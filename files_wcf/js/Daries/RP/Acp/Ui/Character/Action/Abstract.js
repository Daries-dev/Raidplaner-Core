/**
 * An abstract action, to handle character actions.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */
define(["require", "exports"], function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.AbstractCharacterAction = void 0;
    class AbstractCharacterAction {
        button;
        characterDataElement;
        characterId;
        constructor(button, characterId, characterDataElement) {
            this.button = button;
            this.characterDataElement = characterDataElement;
            this.characterId = characterId;
            this.executeAction();
        }
    }
    exports.AbstractCharacterAction = AbstractCharacterAction;
    exports.default = AbstractCharacterAction;
});
