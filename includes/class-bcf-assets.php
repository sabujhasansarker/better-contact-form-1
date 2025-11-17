<?php

class BCF_Assets
{
     public function __construct()
     {
          add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
     }
     public function enqueue_frontend_assets()
     {
          wp_enqueue_style('bcf-frontend', BCF_PLUGIN_URL . 'assets/css/frontend.css', array(), '1.0.0');
     }
}
