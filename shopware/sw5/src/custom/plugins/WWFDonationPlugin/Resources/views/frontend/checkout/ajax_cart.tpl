{*
 Copyright 2020-2021 EXXETA AG, Marius Schuppert

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program. If not, see <https://www.gnu.org/licenses/>.
*}
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