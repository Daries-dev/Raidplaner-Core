/**
 * Search a Item.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { ApiResult, apiResultFromError, apiResultFromValue } from "WoltLabSuite/Core/Api/Result";

export async function searchItem(itemName: string, additionalData: string = ""): Promise<ApiResult<Response>> {
  const url = new URL(`${window.WSC_API_URL}index.php?api/rpc/rp/items/search`);
  url.searchParams.set("itemName", itemName);
  url.searchParams.set("additionalData", additionalData);

  let response: Response;
  try {
    response = (await prepareRequest(url).get().fetchAsJson()) as Response;
  } catch (e) {
    return apiResultFromError(e);
  }

  return apiResultFromValue(response);
}

type Response = {
  itemID: number;
  itemName: string;
};
