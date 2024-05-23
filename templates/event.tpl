{capture assign='pageTitle'}{$event->getTitle()}{/capture}

{capture assign='contentHeader'}
    <header class="contentHeader">
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
                    <span class="badge label green">{lang}wcf.message.status.disabled{/lang}</span>
                {/if}

                {if $event->isDeleted}
                    <span class="badge label red">{lang}wcf.message.status.deleted{/lang}</span>
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
    </header>
{/capture}

{capture assign='contentInteractionShareButton'}
    <button type="button" class="button small wsShareButton jsTooltip" title="{lang}wcf.message.share{/lang}"
        data-link="{$event->getLink()}" data-link-title="{$event->getTitle()}"
        data-bbcode="[rpEvent]{$event->getObjectID()}[/rpEvent]">
        {icon name='share-nodes'}
    </button>
{/capture}

{include file='header'}

{hascontent}
<section class="section">
    <h2 class="sectionTitle">{lang}rp.event.notes{/lang}</h2>

    <dl>
        <dt></dt>
        <dd>
            <div class="htmlContent">
                {content}
                {@$event->getFormattedMessage()}
                {/content}
            </div>
        </dd>
    </dl>
</section>
{/hascontent}

{if !$event->isDeleted && $event->getController()->isExpired()}
    <p class="error">{lang}rp.event.expired{/lang}</p>
{/if}

{if $event->getDeleteNote()}
    <div class="section">
        <p class="rpEventDeleteNote">{@$event->getDeleteNote()}</p>
    </div>
{/if}

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