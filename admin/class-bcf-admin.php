<?php

class BCF_Admin
{
     public function __construct()
     {
          add_action('admin_menu', array($this, 'add_admin_menu'));
          add_action('wp_ajax_bcf_save_form_builder', array($this, 'save_form_builder'));
     }

     public function add_admin_menu()
     {
          add_submenu_page(
               'edit.php?post_type=bcf_submission',
               'Form Builder',
               'Form Builder',
               'manage_options',
               'bcf-form-builder',
               array($this, 'form_builder_page')
          );
     }
     public function form_builder_page()
     {
          $form_fields = get_option('bcf_form_fields', $this->get_default_fields());
?>
          <div class="wrap" x-data="formBuilder" x-init="init(<?php echo esc_attr(json_encode($form_fields)); ?>)">
               <h1>Contact Form Builder</h1>
               <div class="bcf-form-builder">
                    <div class="bcf-builder-content">
                         <h2>Form Fields</h2>
                         <p>Build your custom contact form by adding, removing, or editing fields below.</p>
                         <div class="bcf-fields-list">
                              <template x-for="(field,index) in fields" :key="field.id">
                                   <div class="bcf-field-item" :class="{ 'required-field': field.required }">
                                        <div class="bcf-field-header">
                                             <input type="text" x-model="field.label" placeholder="Field Label" class="bcf-field-label">
                                             <select x-model="field.type" class="bcf-field-type">
                                                  <option value="text">Text</option>
                                                  <option value="email">Email</option>
                                                  <option value="textarea">Textarea</option>
                                                  <option value="tel">Phone</option>
                                                  <option value="number">Number</option>
                                             </select>
                                        </div>
                                        <div class="bcf-field-options">
                                             <label>
                                                  <input type="checkbox" x-model="field.required">
                                                  Required
                                             </label>
                                             <input type="text" x-model="field.placeholder" placeholder="Placeholder text" class="bcf-field-placeholder">
                                             <button type="button" @click="removeField(index)" class="button button-secondary bcf-remove-field" :disabled="field.locked">
                                                  Remove
                                             </button>
                                        </div>
                                   </div>
                              </template>
                              <div class="bcf-add-field">
                                   <button type="button" @click="addField()" class="button button-primary">Add New Field</button>
                              </div>
                              <div class="bcf-form-actions">
                                   <button type="button" class="button button-primary" @click="saveForm()">
                                        Save Form
                                   </button>
                                   <button type="button" class="button button-secondary" @click="resetForm()">Reset to Default</button>
                              </div>
                         </div>
                         <div x-show="message" class="notice" :class="messageType === 'success' ? 'notice-success' : 'notice-error'">
                              <p x-text="message"></p>
                         </div>
                    </div>

                    <div class="bcf-preview">
                         <h3>Form Preview</h3>
                         <div class="bcf-form-preview">
                              <div class="bcf-container">
                                   <h3>Contact Us</h3>
                                   <form class="bcf-form">
                                        <template x-for="field in fields" :key="field.id">
                                             <div class="bcf-field">
                                                  <label x-text="field.label + (field.required ? ' *' : '')"></label>
                                                  <input x-show="field.type !== 'textarea'" :type="field.type" :placeholder="field.placeholder" :required="field.required" disabled>
                                                  <textarea x-show="field.type === 'textarea'" :placeholder="field.placeholder" :required="field.required" rows="5" disabled></textarea>
                                             </div>
                                        </template>
                                        <div class="bcf-field">
                                             <button type="button" class="bcf-submit" disabled>Send Message</button>
                                        </div>
                                   </form>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
          <script>
               document.addEventListener("alpine:init", () => {
                    Alpine.data("formBuilder", () => ({
                         fields: [],
                         message: "",
                         messageType: "success",
                         init(savedFields) {
                              this.fields = savedFields && savedFields.length > 0 ? savedFields : this.getDefaultFields();
                         },
                         getDefaultFields() {
                              return [{
                                        id: "name",
                                        label: "Name",
                                        type: "text",
                                        required: true,
                                        placeholder: "Enter your name",
                                        locked: true,
                                   },
                                   {
                                        id: "email",
                                        label: "Email",
                                        type: "email",
                                        required: true,
                                        placeholder: "Enter your email",
                                        locked: true,
                                   },
                                   {
                                        id: "message",
                                        label: "Message",
                                        type: "textarea",
                                        required: true,
                                        placeholder: "Enter your message",
                                        locked: true,
                                   },
                              ];
                         },
                         removeField(index) {
                              if (!this.fields[index].locked) {
                                   this.fields.splice(index, 1);
                              }
                         },
                         addField() {
                              const newField = {
                                   id: "field_" + Date.now(),
                                   label: "New Field",
                                   type: "text",
                                   required: false,
                                   placeholder: "Enter text",
                                   locked: false,
                              };
                              this.fields.push(newField);
                         },
                         resetForm() {
                              if (
                                   confirm(
                                        "Are you sure you want to reset to default fields? This will remove all custom fields."
                                   )
                              ) {
                                   this.fields = this.getDefaultFields();
                                   this.message = "Form has been reset to default fields.";
                                   this.messageType = "success";
                                   setTimeout(() => (this.message = ""), 3000);
                              }
                         },
                         saveForm() {
                              this.saving = true;
                              this.message = '';

                              fetch(ajaxurl, {
                                        method: 'POST',
                                        headers: {
                                             'Content-Type': 'application/x-www-form-urlencoded',
                                        },
                                        body: new URLSearchParams({
                                             action: 'bcf_save_form_builder',
                                             nonce: '<?php echo wp_create_nonce("bcf_save_form"); ?>',
                                             fields: JSON.stringify(this.fields)
                                        })
                                   })
                                   .then(response => response.json())
                                   .then(data => {
                                        if (data.success) {
                                             this.message = 'Form saved successfully!';
                                             this.messageType = 'success';
                                        } else {
                                             this.message = data.data || 'Error saving form';
                                             this.messageType = 'error';
                                        }
                                   })
                                   .catch(error => {
                                        this.message = 'Network error occurred';
                                        this.messageType = 'error';
                                   })
                                   .finally(() => {
                                        this.saving = false;
                                        setTimeout(() => this.message = '', 3000);
                                   });
                         }
                    }));
               });
          </script>
<?php
     }
     public function save_form_builder()
     {
          if (!wp_verify_nonce($_POST['nonce'], 'bcf_save_form')) {
               wp_send_json_error('Security check failed');
          }
          if (!current_user_can('manage_options')) {
               wp_send_json_error('Insufficient permissions');
          }
          $fields = json_decode(stripslashes($_POST['fields']), true);
          if (!is_array($fields)) {
               wp_send_json_error('Invalid field data');
          }

          $validated_fields = array();
          foreach ($fields as $field) {
               if (!isset($field['id'], $field['label'], $field['type'])) {
                    continue;
               }

               $validated_fields[] = array(
                    'id' => sanitize_text_field($field['id']),
                    'label' => sanitize_text_field($field['label']),
                    'type' => sanitize_text_field($field['type']),
                    'required' => !empty($field['required']),
                    'placeholder' => sanitize_text_field($field['placeholder'] ?? ''),
                    'locked' => !empty($field['locked'])
               );
          }

          update_option('bcf_form_fields', $validated_fields);
          wp_send_json_success('Form saved successfully');
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
               ),
          );
     }
}
