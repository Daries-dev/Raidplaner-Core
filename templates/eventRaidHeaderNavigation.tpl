{if $canParticipate}
    <li>
        <a href="#" class="button buttonPrimary jsEventRaidParticipate" {if $hasAttendee} style="display: none;" {/if}>
            {icon name='plus'}
            <span>{lang}rp.event.raid.participate{/lang}</span>
        </a>
    </li>

    <script data-relocate="true">
        require(['Daries/RP/Ui/Event/Raid/Participate'], function({ EventRaidParticipate }) {
            new EventRaidParticipate({@$event->eventID});
        });
    </script>
{/if}