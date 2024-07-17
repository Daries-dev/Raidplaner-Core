{if $canParticipate}
    <li>
        <a href="#" class="button buttonPrimary jsEventRaidParticipat" {if $hasAttendee} style="display: none;" {/if}>
            {icon name='plus'}
            <span>{lang}rp.event.raid.participate{/lang}</span>
        </a>
    </li>

    <script data-relocate="true">
        require(['Daries/RP/Ui/Event/Raid/Participat'], function({ EventRaidParticipat }) {
            new EventRaidParticipat({@$event->eventID});
        });
    </script>
{/if}