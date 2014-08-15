(function($) {
    "use strict";

    $(document).ready(function() {

        setInterval(setIntervalAjax, (GARP_Ajax.intervalTime * 1000));

        function setIntervalAjax() {
            {
                var widgetUL,
                        countPosts,
                        lastPublishedPost,
                        lastPublishedPostID,
                        firstPublishedPost,
                        firstPublishedPostID;

                widgetUL = $('.widget_garp_widget ul');
                countPosts = $('li', widgetUL).length;

                lastPublishedPost = $('li:first-child', widgetUL);
                lastPublishedPostID = lastPublishedPost.data('garp-post-id');

                firstPublishedPost = $('li:last-child', widgetUL);
                firstPublishedPostID = firstPublishedPost.data('garp-post-id');

                $.post(
                        GARP_Ajax.ajaxurl,
                        {
                            // wp ajax action
                            action: 'gsy-ajax-recent-posts',
                            // vars
                            lastPublishedPostID: lastPublishedPostID,
                            // send the nonce along with the request
                            nextNonce: GARP_Ajax.nextNonce
                        },
                function(response) {
                    var html;

                    if (response.refresh_widget) {
                        if (response.post_action === 'add') {
                            
                            html = '<li data-garp-post-id="' + response.post_data.id + '">';
                            html += '<a href="' + response.post_data.guid + '">' + response.post_data.title + '</a>';
                            if (GARP_Ajax.showDate) {
                                html += '<span class="post-date">' + response.post_data.date + '</span>';
                            }
                            html += '</li>';

                            if (countPosts >= GARP_Ajax.postsToShow) {
                                $('li[data-garp-post-id="' + firstPublishedPostID + '"]').remove();
                            }

                            $(html).hide().prependTo(widgetUL).fadeIn("slow");

                        } else if (response.post_action === 'remove') {
                            $('li[data-garp-post-id="' + lastPublishedPostID + '"]').fadeOut("slow", function() {
                                $(this).remove();
                            });
                        }
                    }
                }
                );
                return false;
            }
        }
    });

})(jQuery);