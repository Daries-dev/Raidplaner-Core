/**
 * Updates a attendee.
 *
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

import { prepareRequest } from "WoltLabSuite/Core/Ajax/Backend";
import { ApiResult, apiResultFromError, apiResultFromValue } from "WoltLabSuite/Core/Api/Result";

export async function updateAttendee(
  attendeeId: number,
  distributionId: number,
  status: string,
): Promise<ApiResult<[]>> {
  try {
    await prepareRequest(`${window.WSC_API_URL}index.php?api/rpc/rp/attendees/${attendeeId}`)
      .post({
        distributionId,
        status,
      })
      .fetchAsJson();
  } catch (e) {
    return apiResultFromError(e);
  }

  return apiResultFromValue([]);
}
