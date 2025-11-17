<?php

class BCF_Ajax
{
     public function __construct()
     {
          add_action('wp_ajax_bcf_submit_form', array($this, 'handle_form_submission'));
          add_action('wp_ajax_nopriv_bcf_submit_form', array($this, 'handle_form_submission'));
     }

     public function handle_form_submission()
     {
          // Verify nonce
          if (!wp_verify_nonce($_POST['bcf_nonce'], 'bcf_submit_form')) {
               wp_send_json_error('Security check failed');
          }

          // Get form fields configuration
          $form_fields = get_option('bcf_form_fields', $this->get_default_fields());

          $form_data = array();
          $required_missing = array();

          // Process each field
          foreach ($form_fields as $field) {
               $field_name = 'bcf_' . $field['id'];
               $field_value = isset($_POST[$field_name]) ? $_POST[$field_name] : '';

               // Sanitize based on field type
               switch ($field['type']) {
                    case 'email':
                         $field_value = sanitize_email($field_value);
                         if ($field_value && !is_email($field_value)) {
                              wp_send_json_error('Please enter a valid email address for ' . $field['label']);
                         }
                         break;
                    case 'textarea':
                         $field_value = sanitize_textarea_field($field_value);
                         break;
                    case 'number':
                    case 'tel':
                         $field_value = sanitize_text_field($field_value);
                         break;
                    default:
                         $field_value = sanitize_text_field($field_value);
               }

               // Check required fields
               if ($field['required'] && empty($field_value)) {
                    $required_missing[] = $field['label'];
               }

               $form_data[$field['id']] = array(
                    'label' => $field['label'],
                    'value' => $field_value,
                    'type' => $field['type']
               );
          }

          // Check for missing required fields
          if (!empty($required_missing)) {
               wp_send_json_error('Required fields missing: ' . implode(', ', $required_missing));
          }

          // Basic spam protection - check for excessive links in any text field
          foreach ($form_data as $data) {
               if (in_array($data['type'], array('text', 'textarea')) && substr_count($data['value'], 'http') > 2) {
                    wp_send_json_error('Message contains too many links');
               }
          }

          // Create submission
          $submission_id = BCF_Post_Type::create_submission($form_data);

          if ($submission_id) {
               wp_send_json_success('Thank you! Your message has been sent successfully.');
          } else {
               wp_send_json_error('Sorry, there was an error sending your message. Please try again.');
          }
     }

     private function get_default_fields()
     {
          return array(
               array(
                    'id' => 'name',
                    'label' => 'Name',
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => '',
                    'locked' => true
               ),
               array(
                    'id' => 'email',
                    'label' => 'Email',
                    'type' => 'email',
                    'required' => true,
                    'placeholder' => '',
                    'locked' => true
               ),
               array(
                    'id' => 'message',
                    'label' => 'Message',
                    'type' => 'textarea',
                    'required' => true,
                    'placeholder' => '',
                    'locked' => true
               )
          );
     }
}
