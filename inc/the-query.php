<?php

$query_args = array(
    'post_type' => 'post',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 2,
    'post__not_in' => get_option('sticky_posts'),
);

// The Query
$the_query = new WP_Query($query_args);