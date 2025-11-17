jQuery(document).ready(function ($) {
  $("#bcf-contact-form").on("submit", function (e) {
    e.preventDefault();
    var form = $(this);
    var submitBtn = form.find(".bcf-submit");
    var messagesDiv = $("#bcf-messages");
    submitBtn.prop("disabled", true).text("Sending...");
    messagesDiv.empty();
    var formData = {
      action: "bcf_submit_form",
      bcf_nonce: bcf_ajax.nonce,
    };
    form.find('input:not([name="bcf_nonce"]), textarea').each(function () {
      var field = $(this);
      var fieldName = field.attr("name");
      if (fieldName && fieldName.startsWith("bcf_")) {
        formData[fieldName] = field.val();
      }
    });
    $.post(bcf_ajax.ajax_url, formData, function (response) {
      if (response.success) {
        messagesDiv.html(
          '<div class="bcf-message success">' + response.data + "</div>"
        );
        form[0].reset(); // Clear form
      } else {
        messagesDiv.html(
          '<div class="bcf-message error">' + response.data + "</div>"
        );
      }
    })
      .fail(function () {
        messagesDiv.html(
          '<div class="bcf-message error">An error occurred. Please try again.</div>'
        );
      })
      .always(function () {
        submitBtn.prop("disabled", false).text("Send Message");
        $("html, body").animate(
          {
            scrollTop: messagesDiv.offset().top - 20,
          },
          500
        );
      });
  });
});
