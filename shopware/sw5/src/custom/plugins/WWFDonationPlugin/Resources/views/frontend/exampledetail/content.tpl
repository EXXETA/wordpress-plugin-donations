{extends file="parent:frontend/detail/content.tpl"}

{block name="frontend_detail_index_header_container"}
    <link type="text/css" media="all" rel="stylesheet" href="{link file='frontend/_resources/css/banner.css'}"/>
    {wwfbanner campaign="protect_species_coin" isAjax=false isMini=true miniBannerTargetPage="https://www.wwf.de"}

    {$smarty.block.parent}
{/block}