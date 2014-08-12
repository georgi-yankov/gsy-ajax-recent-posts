<?php

/*
 * Plugin Name: GSY Ajax Recent Posts
 * Plugin URI: https://github.com/georgi-yankov/gsy-ajax-recent-posts
 * Description: A widget for ajax recent posts
 * Version: 1.0
 * Author: Georgi Yankov
 * Author URI: http://gsy-design.com
 */

include_once 'class-garp-widget.php';

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
        'nextNonce' => wp_create_nonce('myajax-next-nonce'))
    );
}

/* =============================================================================
  AJAX TEST
  =========================================================================== */

add_action('wp_ajax_ajax-inputtitleSubmit', 'myajax_inputtitleSubmit_func');
add_action('wp_ajax_nopriv_ajax-inputtitleSubmit', 'myajax_inputtitleSubmit_func');

function myajax_inputtitleSubmit_func() {
// check nonce
    $nonce = $_POST['nextNonce'];
    if (!wp_verify_nonce($nonce, 'myajax-next-nonce'))
        die('Busted!');

    require 'inc/the-query.php';

    $result = array();
    $post_id = $the_query->posts[0]->ID;

    if ($post_id === (int) $_POST['lastPublishedPostID']) {
        $result['refresh_widget'] = false;
    } else if ($post_id > (int) $_POST['lastPublishedPostID']) {
        $post_title = $the_query->posts[0]->post_title;
        $post_guid = $the_query->posts[0]->guid;
        $post_date = $the_query->posts[0]->post_date;

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

// generate the response
    $response = json_encode($result);

// response output
    header("Content-Type: application/json");
    echo $response;

// IMPORTANT: don't forget to "exit"
    exit;
}