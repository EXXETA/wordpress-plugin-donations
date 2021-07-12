{extends file='parent:frontend/checkout/ajax_cart.tpl'}

{block name='frontend_checkout_ajax_cart_item_container_inner'}
    {is_cart_integration_enabled}
    {if boolval($isOffCanvasCartIntegrationEnabled)}
        <link type="text/css" media="all" rel="stylesheet" href="{link file='frontend/_resources/css/banner.css'}"/>
        <div>
            {wwfbannercartoffcanvas}
        </div>
    {/if}

    {$smarty.block.parent}
{/block}