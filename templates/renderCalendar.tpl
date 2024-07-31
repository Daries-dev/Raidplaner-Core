<div class="rp-calendar">
    <h2 class="rp-calendar-month">{$monthName} {$year}</h2>

    <div>
        <div class="rp-calendar-weekdays">
            {foreach from=$weekDays item=weekDay}
                <div class="rp-calendar-weekday">{lang}wcf.date.day.{$weekDay}{/lang}</div>
            {/foreach}
        </div>

        <div class="rp-calendar-days">
            {foreach from=$days item=day}
                {if $day|empty}
                    <div class="rp-calendar-day rp-calendar-day-empty"></div>
                {else}
                    <div class="rp-calendar-day" data-day="{$day->__toString()}">
                        <div>{$day->getDay()}</div>
                        {if $day->getEvents()}
                            <ul class="rp-calendar-events">
                                {foreach from=$day->getEvents() item=dayEvent}
                                    <li class="rp-calendar-event rpEventPopover{if $dayEvent->isFullDay} rp-calendar-event-full-day{/if}">
                                        {if ($dayEvent->getStatus() === 1)}
                                            {icon name='right-from-bracket'}
                                            <span class="rp-calender-event-time">{$dayEvent->getFormattedStartTime(true)}</span>
                                        {elseif $dayEvent->getStatus() === 2}
                                            {icon name='left-right'}
                                        {elseif $dayEvent->getStatus() === 3}
                                            {icon name='right-to-bracket'}
                                            <span class="rp-calender-event-time">{$dayEvent->getFormattedEndTime(true)}</span>
                                        {else}
                                            {if !$dayEvent->isFullDay}
                                                <span class="rp-calender-event-time">{$dayEvent->getFormattedStartTime(true)}</span>
                                            {/if}
                                        {/if}
                                        <span>{$dayEvent->getTitle()}</span>
                                    </li>
                                {/foreach}
                            </ul>
                        {/if}
                    </div>
                {/if}
            {/foreach}
        </div>
    </div>
</div>

<script data-relocate="true">
    require(['WoltLabSuite/Core/Helper/Selector'], function({ wheneverSeen }) {
        wheneverSeen("[data-event-link]", (element) => {
            element.addEventListener("click", () => {
                window.location = element.dataset.eventLink;
            });
        });
    });
</script>