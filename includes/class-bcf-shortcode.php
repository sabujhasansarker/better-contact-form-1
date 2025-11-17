<?php
class BCF_Shortcode
{
     public function __construct()
     {
          add_shortcode('better-contact-form', array($this, 'render_form'));
     }
     public function render_form($atts)
     {
          $att = shortcode_atts(
               array(
                    'title' => 'Contact Us'
               ),
               $atts
          );
          ob_start();
          echo esc_html($att['title']);
          return ob_get_clean();
     }
}
