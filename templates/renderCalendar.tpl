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
                <div class="rp-calendar-day{if $day|empty} rp-calendar-day-empty{/if}">
                    {if !$day|empty}
                        <div>{$day}</div>
                        {if $events[$day]|isset}
                            <ul class="rp-calendar->events">
                                {foreach from=$events[$day] item=event}
                                    <li class="rp-calendar-event">TODO</li>
                                {/foreach}
                            </ul>
                        {/if}
                    {/if}
                </div>
            {/foreach}
        </div>
    </div>
</div>