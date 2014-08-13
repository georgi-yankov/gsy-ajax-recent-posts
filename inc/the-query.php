<?php

$query_args = array(
    'post_type' => POST_TYPE,
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => POSTS_PER_PAGE,
    'post__not_in' => get_option('sticky_posts'),
);

// The Query
$the_query = new WP_Query($query_args);