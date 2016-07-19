# Список новостей с разным количеством на первой и последующих страницах

На первой странице выводим список новостей с нужным количеством, запоминаем идентификаторы новостей на станице.
Пагинацию начинаем строить со воторой страницы, исключая новости которые были на первой.

### Вызов компонента

```
global $arFilter;
if ($_REQUEST["FIRST_PAGE_IDS"]) { //исключаем новости, которые были на первой странице
    $arFilter = array(
        "!ID" => explode(",",$_REQUEST["FIRST_PAGE_IDS"])
    );
}


$APPLICATION->IncludeComponent("bitrix:news.list", "news.list", array(
    "IBLOCK_TYPE" => "news",
    "IBLOCK_ID" => 1,
    "NEWS_COUNT" => 2,
    "SORT_BY1" => "ID",
    "SORT_ORDER1" => "DESC",
    "SORT_BY2" => "SORT",
    "SORT_ORDER2" => "ASC",
    "FILTER_NAME" => "arFilter",
    "FIELD_CODE" => array(
        "NAME",
    ),
    "PROPERTY_CODE" => array(
    ),
    "CHECK_DATES" => "Y",
    "AJAX_MODE" => "N",
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_STYLE" => "N",
    "AJAX_OPTION_HISTORY" => "N",
    "CACHE_TYPE" => "Y",
    "CACHE_TIME" => "3600",
    "CACHE_FILTER" => "Y",
    "CACHE_GROUPS" => "Y",
    "PREVIEW_TRUNCATE_LEN" => "",
    "ACTIVE_DATE_FORMAT" => "d.m.Y",
    "SET_TITLE" => "Y",
    "SET_STATUS_404" => "Y",
    "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
    "ADD_SECTIONS_CHAIN" => "Y",
    "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
    "PARENT_SECTION" => "",
    "PARENT_SECTION_CODE" => "",
    "DISPLAY_TOP_PAGER" => "N",
    "DISPLAY_BOTTOM_PAGER" => "Y",
    "PAGER_TITLE" => "Новости",
    "PAGER_SHOW_ALWAYS" => "Y",
    "PAGER_TEMPLATE" => "show.more",
    "PAGER_DESC_NUMBERING" => "N",
    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
    "PAGER_SHOW_ALL" => "N",
    "DISPLAY_DATE" => "Y",
    "DISPLAY_NAME" => "Y",
    "DISPLAY_PICTURE" => "Y",
    "DISPLAY_PREVIEW_TEXT" => "Y",
    "AJAX_OPTION_ADDITIONAL" => "",
    "FIRST_PAGE_IDS" => $_REQUEST["FIRST_PAGE_IDS"],
),
    false
);

```
В параметрах компонета укзываем кастомный шаблон пагинации, для реализации кнопки "Показать еще"
```
"PAGER_TEMPLATE" => "show.more",
```
Также передаем идентификаторы нвовостей на первой странице из реквеста
```
"FIRST_PAGE_IDS" => $_REQUEST["FIRST_PAGE_IDS"],
```

### Шаблон компонета списка новостей
```
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
     <?if (!($arParams["FIRST_PAGE_IDS"])) : //сохраняем идентификаторы новостей на первой странице ?>
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
```
### скрипт для запроса на получения контента со следующей страницы
```
(function($) {
    $(function() {
        var container_list_selector = '.news-list',
            show_more_selector = '.show-more',
            page_number = 0;

        $('body').on ('click', show_more_selector, function (e) {
            var $container_list_selector = $(container_list_selector),
                $this = $(this),
                path = $this.attr('href'),
                first_page_ids = $container_list_selector.data("first_page_ids");
            
            page_number++;
            
            if (page_number == 1) {//если самая первая страница, 
            //то делаем запрос опять на первую страницу, 
            //только уже исключая новости которые уже отобразили,
                path = $this.data("first_page_url");
            }
            
            $.ajax({
                url : path,
                method: "POST",
                data : {'FIRST_PAGE_IDS' : first_page_ids},
                success : function (data) {
                    var element = document.createElement('div'),
                        $domElement = $(element);
                    $domElement.html(data);
                    $container_list_selector.append($domElement.find(container_list_selector).html());
                    var $newShowMore = $domElement.find(show_more_selector);
                    if ($domElement.find(show_more_selector).length) {
                        $this.attr('href', $newShowMore.attr('href'));
                    } else {
                        $this.hide();
                    }
                }
            });

            e.preventDefault();
        });
    });
})(jQuery);
```
### Шаблон компонента пагинации
В ссылке необходимо передать data атрибут
```
data-first_page_url="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=1&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>"
```
