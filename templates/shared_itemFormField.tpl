{include file='__formFieldErrors'}

<div class="row rowColGap formGrid">
    <dl class="col-xs-12 col-md-4">
        <dt><label name="{@$field->getPrefixedId()}_itemName">{lang}rp.item.itemName{/lang}</label></dt>
        <dd>
            <input type="text" id="{@$field->getPrefixedId()}_itemName" class="long">
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-2">
        <dt><label name="{@$field->getPrefixedId()}_pointAccount">{lang}rp.item.pointAccount{/lang}</label></dt>
        <dd>
            <select id="{@$field->getPrefixedId()}_pointAccount">
                {foreach from=$field->getPointAccounts() item=__pointAccount}
                    <option {*
                        *}value="{$__pointAccount->getObjectID()}" {*
                        *}{if $field->getValue() == $__pointAccount->pointAccountID} selected{/if}{*
                    *}>{$__pointAccount->getTitle()}</option>
                {/foreach}
            </select>
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-2">
        <dt><label name="{@$field->getPrefixedId()}_character">{lang}rp.item.character{/lang}</label></dt>
        <dd>
            <select id="{@$field->getPrefixedId()}_character">
                {foreach from=$field->getCharacters() item=__character}
                    <option {*
                        *}value="{$__character->getObjectID()}" {*
                        *}{if $field->getValue() == $__character->getObjectID()} selected{/if}{*
                    *}>{$__character->getTitle()}</option>
                {/foreach}
            </select>
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-2">
        <dt><label name="{@$field->getPrefixedId()}_points">{lang}rp.item.points{/lang}</label></dt>
        <dd>
            <input type="text" id="{@$field->getPrefixedId()}_points" class="long">
        </dd>
    </dl>
    <dl class="col-xs-12 col-md-1">
        <dt></dt>
        <dd>
            <a href="#" class="button small" id="{@$field->getPrefixedId()}_addButton">{lang}wcf.global.button.add{/lang}</a>
        </dd>
    </dl>
</div>

<ol class="nativeList rpItemList" id="{@$field->getPrefixedId()}_itemList"></ol>

<script data-relocate="true">
    require(['Daries/RP/Form/Builder/Field/Item'], function(ItemFormField) {
        WoltLabLanguage.registerPhrase("rp.item.form.field", '{jslang __literal=true}rp.item.form.field{/jslang}');
        {jsphrase name='rp.item.points.error.format'}

        new ItemFormField('{@$field->getPrefixedId()}', [
            {implode from=$field->getValue() item=item}
                {
                    characterId: '{$item[characterID]}',
                    characterName: '{$item[characterName]|encodeJS}',
                    itemId: '{$item[itemID]}',
                    itemName: '{$item[itemName]|encodeJS}',
                    pointAccountId: '{$item[pointAccountID]}',
                    pointAccountName: '{$item[pointAccountName]|encodeJS}',
                    points: '{$item[points]}'
                }
            {/implode}
        ]);
    });
</script>