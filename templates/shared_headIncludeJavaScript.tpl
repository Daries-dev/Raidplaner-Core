<script data-relocate="true">
    require(['Daries/RP/BootstrapFrontend'], function(BootstrapFrontend) {
        {jsphrase name='rp.event.raid.container.login'}
        {jsphrase name='rp.event.raid.container.logout'}
        {jsphrase name='rp.event.raid.container.reserve'}
        {jsphrase name='rp.event.raid.status'}
        {jsphrase name='rp.event.raid.updateStatus'}

        BootstrapFrontend.setup({
            RP_API_URL: '{@$__wcf->getPath('rp')}',
        });
    });
</script>