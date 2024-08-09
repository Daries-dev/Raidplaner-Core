{capture assign='pageTitle'}{$event->getTitle()}{/capture}

{capture assign='contentHeader'}
    <header class="contentHeader rpEventHeader" data-object-id="{@$event->eventID}" data-is-deleted="{@$event->isDeleted}"
        data-is-disabled="{@$event->isDisabled}">
        <div class="contentHeaderIcon">
            {@$event->getIcon(64)}
        </div>

        <div class="contentHeaderTitle">
            <h1 class="contentTitle">
                {$event->getTitle()}

                {if $event->isNew()}
                    <span class="badge label green">{lang}wcf.message.new{/lang}</span>
                {/if}

                {if $event->isDisabled}
                    <span class="badge label green jsIsDisabled">{lang}wcf.message.status.disabled{/lang}</span>
                {/if}

                {if $event->isDeleted}
                    <span class="badge label red jsIsDeleted">{lang}wcf.message.status.deleted{/lang}</span>
                {/if}
            </h1>
            <ul class="inlineList commaSeparated contentHeaderMetaData">
                <li>
                    {icon name='clock'}
                    {@$event->getFormattedStartTime()} - {@$event->getFormattedEndTime()}
                </li>
                <li>
                    {icon name='user'}
                    {user object=$event->getUserProfile()}
                </li>
                <li>
                    {icon name='eye'}
                    {lang}rp.event.views{/lang}
                </li>
            </ul>
        </div>

        {hascontent}
        <nav class="contentHeaderNavigation">
            <ul>
                {content}
                {@$event->getController()->getContentHeaderNavigation()}
                {/content}
            </ul>
        </nav>
        {/hascontent}
    </header>
{/capture}

{capture assign='contentInteractionShareButton'}
    <button type="button" class="button small wsShareButton jsTooltip" title="{lang}wcf.message.share{/lang}"
        data-link="{$event->getLink()}" data-link-title="{$event->getTitle()}"
        data-bbcode="[rpEvent]{$event->getObjectID()}[/rpEvent]">
        {icon name='share-nodes'}
    </button>
{/capture}

{if $event->getController()->showEventNodes('right')}
    {hascontent}
    {capture append='sidebarRight'}
        <section class="box" data-static-box-identifier="dev.daries.rp.notes">
            <h2 class="boxTitle">{lang}rp.event.notes{/lang}</h2>

            <div class="boxContent htmlContent">
                {content}
                {@$event->getSimplifiedFormattedNotes()}
                {/content}
            </div>
        </section>
    {/capture}
    {/hascontent}
{/if}

{capture assign='contentInteractionButtons'}
    {if $event->canEdit() || $event->canEditOwnEvent()}
        <div id="eventDropdown" class="contentInteractionButton dropdown jsOnly eventDropdown">
            <button type="button" class="button small dropdownToggle">
                {icon name='sliders'}
                <span>{lang}rp.event.settings{/lang}</span>
            </button>
            <ul class="dropdownMenu">
                <li hidden>
                    <a href="#" class="jsDelete">{lang}rp.event.delete{/lang}</a>
                </li>
                <li hidden>
                    <a href="#" class="jsRestore">{lang}rp.event.restore{/lang}</a>
                </li>
                <li hidden>
                    <a href="#" class="jsTrash">{lang}rp.event.trash{/lang}</a>
                </li>
                <li>
                    <a href="#" class="jsEnable" data-disable-message="{lang}rp.event.disable{/lang}"
                        data-enable-message="{lang}rp.event.enable{/lang}">
                        {lang}rp.event.{if $event->isDisabled}enable{else}disable{/if}{/lang}
                    </a>
                </li>
                <li class="dropdownDivider"></li>
                <li>
                    <a href="{link controller='EventEdit' application='rp' id=$event->eventID}{/link}" class="jsEditLink">
                        {lang}rp.event.edit{/lang}
                    </a>
                </li>
            </ul>
        </div>
    {/if}
{/capture}

{event name='beforeHeader'}

{include file='header'}

{event name='afterHeader'}

{if $event->getController()->showEventNodes('center')}
    {hascontent}
    <section class="section">
        <h2 class="sectionTitle">{lang}rp.event.notes{/lang}</h2>

        <dl>
            <dt></dt>
            <dd>
                <div class="htmlContent">
                    {content}
                    {@$event->getFormattedNotes()}
                    {/content}
                </div>
            </dd>
        </dl>
    </section>
    {/hascontent}
{/if}

{if !$event->isDeleted && $event->getController()->isExpired()}
    <woltlab-core-notice type="error">{lang}rp.event.expired{/lang}</woltlab-core-notice>
{/if}

{if $event->getDeleteNote()}
    <div class="section">
        <p class="rpEventDeleteNote">{@$event->getDeleteNote()}</p>
    </div>
{/if}

<div id="event{@$event->eventID}" class="event" data-can-delete="{if $event->canTrash()}true{else}false{/if}"
    data-can-edit="{if $event->canEdit() || $event->canEditOwnEvent()}true{else}false{/if}"
    data-deleted="{if $event->isDeleted}true{else}false{/if}"
    data-enabled="{if !$event->isDisabled}true{else}false{/if}" data-event-id="{@$event->eventID}"
    data-title="{$event->getTitle()}">
    {@$event->getController()->getContent()}
</div>

<footer class="contentFooter">
    {hascontent}
    <nav class="contentFooterNavigation">
        <ul>
            {content}
            {event name='contentFooterNavigation'}
            {/content}
        </ul>
    </nav>
    {/hascontent}
</footer>

{include file='footer'}

{if $event->canEdit() || $event->canEditOwnEvent()}
    <script data-relocate="true">
        require(['Daries/RP/Ui/Event/Editor'], function({ UiEventEditor }) {
            {jsphrase name='wcf.message.status.deleted'}
            {jsphrase name='wcf.message.status.disabled'}

            new UiEventEditor();
        });
    </script>
{/if}