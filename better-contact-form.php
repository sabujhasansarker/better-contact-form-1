<?php

/**
 * Plugin Name: Better contact form
 * Description: A minimal contact form plugin with Ajax submission and CPT storage
 * Version: 1.0.0
 * Author: Sabuj
 */

if (!defined('ABSPATH')) {
     exit;
}

define('BCF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BCF_PLUGIN_PATH', plugin_dir_path(__FILE__));

/** Initialize plugin */
require_once BCF_PLUGIN_PATH . 'includes/class-bcf-post-type.php';
require_once BCF_PLUGIN_PATH . 'includes/class-bcf-shortcode.php';
require_once BCF_PLUGIN_PATH . 'includes/class-bcf-assets.php';
require_once BCF_PLUGIN_PATH . 'includes/class-bcf-ajax.php';
require_once BCF_PLUGIN_PATH . 'admin/class-bcf-admin.php';
class BCF_Plugin
{
     public function __construct()
     {
          add_action('init', array($this, 'init'));
     }
     public function init()
     {
          $bcf = new BCF_Post_Type();
          $bcf->register_post_type();
          new BCF_Shortcode();
          new BCF_Assets();
          new BCF_Admin();
          new BCF_Ajax();
     }
}
new BCF_Plugin();
