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


{include file='header'}

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
    <p class="error">{lang}rp.event.expired{/lang}</p>
{/if}

{if $event->getDeleteNote()}
    <div class="section">
        <p class="rpEventDeleteNote">{@$event->getDeleteNote()}</p>
    </div>
{/if}

{@$event->getController()->getContent()}

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