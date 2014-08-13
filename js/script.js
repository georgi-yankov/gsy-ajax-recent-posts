(function($) {
    "use strict";

    $(document).ready(function() {
        setInterval(setIntervalAjax, GARP_Ajax.intervalTime);

        function setIntervalAjax() {
            {
                var widgetUL,
                        lastPublishedPost,
                        lastPublishedPostID;

                widgetUL = $('.widget_garp_widget ul');
                lastPublishedPost = $('li:first-child', widgetUL);
                lastPublishedPostID = lastPublishedPost.data('garp-post-id');

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
                            html =
                                    '<li data-garp-post-id="' + response.post_data.id + '">' +
                                    '<a href="' + response.post_data.guid + '">' + response.post_data.title + '</a>' +
                                    '<span class="post-date">' + response.post_data.date + '</span>' +
                                    '</li>';

                            widgetUL.prepend(html);
                        } else if (response.post_action === 'remove') {
                            $('li[data-garp-post-id="' + lastPublishedPostID + '"]').remove();
                        }
                    }
                }
                );
                return false;
            }
        }
    });

})(jQuery);