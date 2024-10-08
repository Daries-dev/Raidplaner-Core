<?php

namespace rp\system\event\listener;

use rp\event\raid\AddAttendeesChecking;
use rp\system\cache\runtime\CharacterRuntimeCache;

/**
 * Handles the `AddAttendeesChecking` event to process attendees data.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class DefaultAddAttendeesChecking
{
    public function __invoke(AddAttendeesChecking $event)
    {
        $attendeeIDs = $event->getAttendeeIDs();

        foreach ($attendeeIDs as $attendeeID) {
            $character = CharacterRuntimeCache::getInstance()->getObject($attendeeID);
            if ($character === null) {
                continue;
            }

            $event->setAttendee([
                'characterID' => $character->getObjectID(),
                'characterName' => $character->getTitle(),
                'classificationID' => $character->classificationID,
                'roleID' => $character->roleID,
            ]);
        }
    }
}
