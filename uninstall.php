<?php

// If uninstall/delete not called from WordPress then exit
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

delete_option('widget_garp_widget');