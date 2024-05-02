{include file='header'}

{if $errorField == 'search'}
    <woltlab-core-notice type="error">{lang}rp.character.search.error.noMatches{/lang}</woltlab-core-notice>
{/if}

{@$form->getHtml()}

{include file='footer'}