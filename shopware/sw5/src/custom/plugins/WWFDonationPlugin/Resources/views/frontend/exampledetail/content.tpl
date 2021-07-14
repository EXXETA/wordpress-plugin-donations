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
{extends file="parent:frontend/detail/content.tpl"}

{block name="frontend_detail_index_header_container"}
    <link type="text/css" media="all" rel="stylesheet" href="{link file='frontend/_resources/css/banner.css'}"/>
    {wwfbanner campaign="protect_species_coin" isAjax=false isMini=true miniBannerTargetPage="https://www.wwf.de"}

    {$smarty.block.parent}
{/block}