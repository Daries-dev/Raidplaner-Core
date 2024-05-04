{capture assign="contentHeader"}
    <header class="contentHeader">
        <nav class="contentHeaderNavigation">
            <ul>
                <a href="{$lastMonthLink}" class="button">
                    {icon name='angles-left'}
                </a>
                <a href="{$currentLink}" class="button">
                    <span>{lang}wcf.date.period.today{/lang}</span>
                </a>
                <a href="{$nextMonthLink}" class="button">
                    {icon name='angles-right'}
                </a>
            </ul>
        </nav>
    </header>
{/capture}

{include file='header'}

{@$calendar}

{include file='footer'}