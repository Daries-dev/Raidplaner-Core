<daries-rp-attendee-drag-and-drop-item id="attendee{@$attendee->attendeeID}"
    class="attendee{if $event->getController()->isLeader()} draggable{/if}" attendee-id="{@$attendee->attendeeID}"
    character-id="{@$attendee->characterID}" distribution-id="{$__availableDistributionID}"
    {if $event->getController()->isLeader()}draggable="true" {/if}
    droppable-to="{implode from=$attendee->getPossibleDistribution() item=distributionID}distribution{@$distributionID}{/implode}"
    user-id="{@$attendee->getCharacter()->userID}">
    <div class="box24">
        <div class="attendeeName">
            {@$attendee->getCharacter()->getAvatar()->getImageTag(24)}
            <span>
                <a href="{$attendee->getLink()}" class="rpEventRaidAttendeeLink"
                    data-object-id="{@$attendee->attendeeID}">{$attendee->getCharacter()->characterName}
                </a>
            </span>
        </div>

        <span class="statusDisplay">
            {if !$attendee->notes|empty}
                <span class="tooltip" title="{$attendee->notes}">
                    {icon name='comment'}
                </span>
            {/if}
            {if !$attendee->characterID}
                <span class="tooltip" title="{lang}rp.event.raid.attendee.guest{/lang}">
                    {icon name='user'}
                </span>
            {/if}
            {if $attendee->addByLeader}
                <span class="tooltip" title="{lang}rp.event.raid.attendee.addByLeader{/lang}">
                    {icon name='plus-circle'}
                </span>
            {/if}
            {if !$event->isCanceled && 
                !$event->isClosed && 
                $event->startTime >= TIME_NOW &&
                $attendee->getCharacter()->userID == $__wcf->user->userID}
            <div id="attendreeDropdown{@$attendee->attendeeID}" class="dropdown">
                <a class="dropdownToggle">
                    {icon name='cog'}
                </a>
                <ul class="dropdownMenu">
                    <li><a class="jsAttendeeUpdateStatus">{lang}rp.event.raid.updateStatus{/lang}</a></li>
                    <li>
                        <a class="jsAttendeeRemove"
                            data-confirm-message-html="{lang __encode=true}rp.event.raid.attendee.remove.confirmMessage{/lang}">
                            {lang}rp.event.raid.attendee.remove{/lang}
                        </a>
                    </li>
                </ul>
            </div>
            {/if}
        </span>
    </div>
</daries-rp-attendee-drag-and-drop-item>