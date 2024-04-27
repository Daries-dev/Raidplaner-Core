/**
 * Provides suggestions for characters.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Free License <https://daries.dev/en/license-for-free-plugins>
 */

import * as Core from "WoltLabSuite/Core/Core";
import { SearchInputOptions } from "WoltLabSuite/Core/Ui/Search/Data";
import UiSearchInput from "WoltLabSuite/Core/Ui/Search/Input";

export class UiCharacterSearchInput extends UiSearchInput {
  constructor(element: HTMLInputElement, options: SearchInputOptions) {
    options = Core.extend(
      {
        ajax: {
          className: "rp\\data\\character\\CharacterAction", //TODO className missing in UISearchInput
        },
      },
      options,
    ) as SearchInputOptions;

    super(element, options);
  }

  protected createListItem(item: CharacterListItemData): HTMLLIElement {
    const listItem = super.createListItem(item);

    const box = document.createElement("div");
    box.className = "box16";
    box.innerHTML = item.icon;
    box.appendChild(listItem.children[0]);
    listItem.appendChild(box);

    return listItem;
  }
}

export default UiCharacterSearchInput;

// https://stackoverflow.com/a/50677584/782822
// This is a dirty hack, because the ListItemData cannot be exported for compatibility reasons.
type FirstArgument<T> = T extends (arg1: infer U, ...args: unknown[]) => unknown ? U : never;

interface CharacterListItemData extends FirstArgument<UiSearchInput["createListItem"]> {
  icon: string;
}
