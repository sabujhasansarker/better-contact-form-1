<?php

class BCF_Assets
{
     public function __construct()
     {
          add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
          add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
     }
     public function enqueue_frontend_assets()
     {
          wp_enqueue_style('bcf-frontend', BCF_PLUGIN_URL . 'assets/css/frontend.css', array(), '1.0.0');
          wp_enqueue_script('bcf-frontend', BCF_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), '1.0.0', true);
          wp_localize_script('bcf-frontend', 'bcf_ajax', array(
               'ajax_url' => admin_url('admin-ajax.php'),
               'nonce' => wp_create_nonce('bcf_submit_form')
          ));
     }

     public function enqueue_admin_assets($hook)
     {
          if (strpos($hook, 'bcf-dashboard') !== false || strpos($hook, 'bcf-form-builder') !== false || get_post_type() === 'bcf_submission') {
               wp_enqueue_style('bcf-admin', BCF_PLUGIN_URL . 'assets/css/admin.css', array(), '1.0.0');
          }
          if (strpos($hook, 'bcf-form-builder') !== false) {
               wp_enqueue_script('alpine-js', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', array(), '3.0.0', true);
               wp_script_add_data('alpine-js', 'defer', true);
          }
     }
}
