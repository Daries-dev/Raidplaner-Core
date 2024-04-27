{include file='shared_textFormField'}

<script data-relocate="true">
    require(['Daries/RP/Ui/Character/Search/Input'], ({ UiCharacterSearchInput }) => {
        new UiCharacterSearchInput(document.getElementById('{@$field->getPrefixedId()}'));
    });
</script>