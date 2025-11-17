<?php

class BCF_Post_Type
{
     public function __construct()
     {
          add_action('init', array($this, 'register_post_type'));
     }
     public function register_post_type()
     {
          $args = array(
               'label' => 'Contact Submissions',
               'labels' => array(
                    'name' => 'Contact Submissions',
                    'singular_name' => 'Contact Submission',
                    'add_new' => 'Add New',
                    'add_new_item' => 'Add New Submission',
                    'edit_item' => 'Edit Submission',
                    'new_item' => 'New Submission',
                    'view_item' => 'View Submission',
                    'search_items' => 'Search Submissions',
                    'not_found' => 'No submissions found',
                    'not_found_in_trash' => 'No submissions found in trash'
               ),
               'public' => false,
               'show_ui' => true,
               'show_in_menu' => true,
               'capability_type' => 'post',
               'capabilities' => array(
                    'create_posts' => false,
               ),
               'map_meta_cap' => true,
               'supports' => array('title', 'editor'),
               'menu_icon' => 'dashicons-email-alt'
          );

          register_post_type('bcf_submission', $args);
     }
     public static function create_submission($form_data)
     {
          // Extract name and email for title (fallback to first available data)
          $name = '';
          $email = '';

          if (isset($form_data['name']['value'])) {
               $name = $form_data['name']['value'];
          }
          if (isset($form_data['email']['value'])) {
               $email = $form_data['email']['value'];
          }

          // If name/email not found, try to find from other fields
          if (empty($name) || empty($email)) {
               foreach ($form_data as $field_id => $field_data) {
                    if (empty($name) && $field_data['type'] === 'text' && !empty($field_data['value'])) {
                         $name = $field_data['value'];
                    }
                    if (empty($email) && $field_data['type'] === 'email' && !empty($field_data['value'])) {
                         $email = $field_data['value'];
                    }
               }
          }

          // Create title
          $title = 'Contact Submission';
          if ($name && $email) {
               $title = sprintf('Contact from %s (%s)', sanitize_text_field($name), sanitize_email($email));
          } elseif ($name) {
               $title = sprintf('Contact from %s', sanitize_text_field($name));
          } elseif ($email) {
               $title = sprintf('Contact from %s', sanitize_email($email));
          }

          // Build content from all form fields
          $content_parts = array();
          foreach ($form_data as $field_id => $field_data) {
               if (!empty($field_data['value'])) {
                    $content_parts[] = sprintf(
                         "%s: %s",
                         esc_html($field_data['label']),
                         esc_html($field_data['value'])
                    );
               }
          }

          $post_data = array(
               'post_title' => $title,
               'post_content' => implode("\n", $content_parts),
               'post_status' => 'publish',
               'post_type' => 'bcf_submission'
          );

          return wp_insert_post($post_data);
     }
}
