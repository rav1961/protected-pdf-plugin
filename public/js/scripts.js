(function ($) {
  'use strict';

  var isFormSending = false;

  function toggleDisabledForm(isDisabled) {
    if (isDisabled === true) {
      isFormSending = true;
      $('#protected-pdf-signin input').attr('disabled', true);
    } else {
      isFormSending = false;
      $('#protected-pdf-signin input').attr('disabled', false);
    }
  }

  function submitSignInForm() {
    var memberData = $('#protected-pdf-signin').serializeArray();

    $('#protected-pdf-signin').on('submit', function (e) {
      e.preventDefault();

      if (isFormSending) {
        return false;
      }

      toggleDisabledForm(true);

      $.ajax({
        type: 'POST',
        dataType: 'json',
        url: this.action,
        data: memberData,
        before: function () {
          $('#protected-pdf-result').removeClass([
            'pdf-form__msg--success',
            'pdf-form__msg--error',
          ]);
        },
        success: function (response) {
          $('#protected-pdf-signin').remove();
          $('#protected-pdf-result')
            .addClass('pdf-form__msg--success')
            .html('<p>' + response + '</p>');

          setTimeout(function () {
            window.location.reload();
          }, 1000);
        },
        error: function (err) {
          $('#protected-pdf-result')
            .addClass('pdf-form__msg--error')
            .html('<p>' + err.responseJSON + '</p>');
        },
        complete: function () {
          toggleDisabledForm(false);
        },
      });

      return false;
    });
  }

  $(window).on('load', function () {
    submitSignInForm();
  });
})(jQuery);
