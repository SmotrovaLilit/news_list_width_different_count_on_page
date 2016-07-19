<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
$ajax = false;
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $ajax = true;
}
global $APPLICATION;
?>
<?php if ($ajax) {
    $APPLICATION->RestartBuffer();
} 
?>
<div class="news-list" 
     <?if (!($arParams["FIRST_PAGE_IDS"])) : ?>
        data-first_page_ids="<?=implode(",",array_column($arResult['ITEMS'] , "ID"))?>"
     <? else :?>
         data-first_page_ids="<?=$arParams["FIRST_PAGE_IDS"]?>"
    <? endif;?>
     
>
<?php foreach ($arResult['ITEMS'] as $cnt => $arItem): ?>
        <div class="item">
            <?= $arItem['NAME'] ?>
        </div>
<?php endforeach; ?>
</div>

<div style="clear:both;">
    <?=$arResult["NAV_STRING"];?>
</div>
<?php if ($ajax) {
    exit;
} ?>