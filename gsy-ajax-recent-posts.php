<?php

/*
 * Plugin Name: GSY Ajax Recent Posts
 * Plugin URI: https://github.com/georgi-yankov/gsy-ajax-recent-posts
 * Description: A widget for ajax recent posts
 * Version: 1.0
 * Author: Georgi Yankov
 * Author URI: http://gsy-design.com
 */

require_once 'config-garp-widget.php';
require_once 'class-garp-widget.php';

/* =============================================================================
  REGISTER GARP_Widget widget
  =========================================================================== */

add_action('widgets_init', 'garp_register_widget');

function garp_register_widget() {
    register_widget('GARP_Widget');
}

/* =============================================================================
  ADDING CSS AND JS
  =========================================================================== */

add_action('wp_enqueue_scripts', 'garp_adding_styles');
add_action('wp_enqueue_scripts', 'garp_adding_scripts');

function garp_adding_styles() {
    $style_src = plugins_url('css/style.css', __FILE__);
    wp_enqueue_style('garp-style', $style_src);
}

function garp_adding_scripts() {
    $script_src = plugins_url('js/script.js', __FILE__);
    wp_enqueue_script('garp-script', $script_src, array('jquery'));
    wp_localize_script('garp-script', 'GARP_Ajax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nextNonce' => wp_create_nonce('garp-myajax-next-nonce'),
        'intervalTime' => INTERVAL_TIME
        )
    );
}

/* =============================================================================
  AJAX
  =========================================================================== */

add_action('wp_ajax_gsy-ajax-recent-posts', 'garp_myajax_func');
add_action('wp_ajax_nopriv_gsy-ajax-recent-posts', 'garp_myajax_func');

function garp_myajax_func() {
// check nonce
    $nonce = $_POST['nextNonce'];
    if (!wp_verify_nonce($nonce, 'garp-myajax-next-nonce')) {
        die('Busted!');
    }

    require 'inc/the-query.php';

    $result = generate_result($the_query);
    
// generate the response
    $response = json_encode($result);

// response output
    header("Content-Type: application/json");
    echo $response;

// IMPORTANT: don't forget to "exit"
    exit;
}

// generate the result
function generate_result($the_query) {
    $result = array();
    $post_id = $the_query->posts[0]->ID;

    if ($post_id === (int) $_POST['lastPublishedPostID']) {
        $result['refresh_widget'] = false;
    } else if ($post_id > (int) $_POST['lastPublishedPostID']) {
        $post_title = $the_query->posts[0]->post_title;
        $post_guid = $the_query->posts[0]->guid;

        $post_date_array = date_parse($the_query->posts[0]->post_date);
        $dateObj = DateTime::createFromFormat('!m', $post_date_array['month']);
        $month_name = $dateObj->format('F');
        $post_date = $month_name . ' ' . $post_date_array['day'] . ', ' . $post_date_array['year'];

        $result = array(
            'refresh_widget' => true,
            'post_action' => 'add',
            'post_data' => array(
                'id' => $post_id,
                'title' => $post_title,
                'guid' => $post_guid,
                'date' => $post_date,
            )
        );
    } else if ($post_id < (int) $_POST['lastPublishedPostID']) {
        $result = array(
            'refresh_widget' => true,
            'post_action' => 'remove',
            'post_data' => array(
                'id' => $post_id,
            )
        );
    }
    
    return $result;
}