/**
 * Create a new attendee.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { ApiResult, apiResultFromError, apiResultFromValue } from "WoltLabSuite/Core/Api/Result";

export async function renderAttendee(attendeeId: number): Promise<ApiResult<Response>> {
  const url = new URL(`${window.WSC_API_URL}index.php?api/rpc/rp/attendees/render`);
  url.searchParams.set("attendeeID", attendeeId.toString());

  let response: Response;
  try {
    response = (await prepareRequest(url).get().fetchAsJson()) as Response;
  } catch (e) {
    return apiResultFromError(e);
  }

  return apiResultFromValue(response);
}

type Response = {
  distributionId: number;
  template: string;
};
