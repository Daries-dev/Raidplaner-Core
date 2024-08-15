{include file='shared_selectFormField'}

<script data-relocate="true">
    require(['Daries/RP/Form/Builder/Field/DynamicSelectManager'], function({ DynamicSelectManager }) {
        new DynamicSelectManager(
            document.getElementById('{unsafe:$field->getTriggerSelect()|encodeJS}'),
            document.getElementById('{unsafe:$field->getPrefixedId()|encodeJS}'),
            {
                {implode from=$field->getOptionsMapping() key='__key' item='__values'}
                {$__key}: [ {implode from=$__values item='__value'}{$__value}{/implode} ]
                {/implode}
            }
        );
    });
</script>