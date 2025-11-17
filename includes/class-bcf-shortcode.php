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
          $default_fields = get_option('bcf_form_fields', $this->get_default_fields());
          ob_start();
?>
          <div class="bcf-container">
               <h3><?php echo esc_html($att['title']) ?></h3>
               <div id="bcf-messages"></div>
               <form>
                    <?php foreach ($default_fields as $field): ?>
                         <div class="bcf-field">
                              <label for="bcf-<?php echo esc_attr($field['id']) ?>">
                                   <?php echo esc_html($field['label']) ?>
                                   <?php if ($field['required']) : ?> * <?php endif ?>
                              </label>
                              <?php if ($field['type'] === 'textarea') : ?>
                                   <textarea
                                        id="bcf_<?php echo esc_attr($field['id']) ?> 
                                        name=" bcf_<?php echo esc_attr($field['id']) ?>"
                                        rows="5" <?php if ($field['required']): ?>required<?php endif; ?>
                                        placeholder="<?php $field['placeholder'] ?>">
                                   </textarea>
                              <?php else : ?>
                                   <input
                                        id="bcf_<?php echo esc_attr($field['id']) ?> 
                                        name=" bcf_<?php echo esc_attr($field['id']) ?>"
                                        type="<?php $field['type'] ?>"
                                        placeholder="<?php $field['placeholder'] ?>">
                              <?php endif ?>
                         </div>
                    <?php endforeach; ?>
                    <div class="bcf-field">
                         <button type="submit" class="bcf-submit">Send Message</button>
                    </div>
               </form>
          </div>
<?php
          return ob_get_clean();
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
