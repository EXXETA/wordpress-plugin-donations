{extends file="parent:frontend/checkout/cart.tpl"}

{block name="frontend_index_header_css_screen"}
    {$smarty.block.parent}
    <link type="text/css" media="all" rel="stylesheet" href="{link file='frontend/_resources/css/banner.css'}"/>
{/block}

{block name="frontend_index_content"}
    {block name="frontend_checkout_cart_wwf_banner"}
        {is_cart_integration_enabled}
        {if boolval($isCartIntegrationEnabled)}
            <div>
                {wwfbannercart}
            </div>
        {/if}
    {/block}
    {$smarty.block.parent}
{/block}