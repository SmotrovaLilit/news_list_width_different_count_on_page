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
            
            if (page_number == 1) {
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