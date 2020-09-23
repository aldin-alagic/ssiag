import jQuery from "jquery";
window.$ = window.jQuery = jQuery;

jQuery(document).ready(function($) {
  $("#login-form").submit(function() {
    $("#login-response").slideUp();
    $("#loader").slideDown();
    $.ajax({
      data: $(this).serialize(),
      type: $(this).attr("method"),
      url: $(this).attr("action"),
      success: function(response) {
        if (response.status == true) {
          const user = response.data;
          sessionStorage.setItem("user", JSON.stringify(user));

          $("#login-response-success").slideDown();
          $("#loader").slideUp(3000, function() {
            $("#login-response-success").slideUp();
            location.href = "./home";
          });
        } else {
          $("#login-response").slideDown();
          $("#loader").slideUp();
        }
      },
      error: function(xhr, desc, err) {
        $("#login-response").text(err);
        $("#loader").hide();
      },
    });
    return false;
  });

  $("#registration-form").submit(function() {
    $("#registration-response").slideUp();

    if ($("#password").val() != $("#password-confirm").val()) {
      $("#registration-response").text(
        "Passwords do not match! Please try again."
      );
      $("#registration-response").slideDown();
      return false;
    }

    $("#loader").slideDown();
    $.ajax({
      data: $(this).serialize(),
      type: $(this).attr("method"),
      url: $(this).attr("action"),
      success: function(response) {
        console.log(JSON.stringify(response));
        if (response.status == true) {
          $("#registration-success").slideDown();
          $("#loader").slideUp(3000, function() {
            $("#registration-success").slideUp();
            location.href = "./login";
          });
        } else {
          console.log(JSON.stringify(response));
          $("#registration-response").text(
            "Incorrect input values. Please try again."
          );
          $("#registration-response").slideDown();
          $("#loader").slideUp();
        }
      },
      error: function(xhr, desc, err) {
        $("#registration-response").text(err);
        $("#loader").hide();
      },
    });
    return false;
  });

  $("#reset-password-form").submit(function() {
    $("#reset-password-response").slideUp();
    $("#loader").slideDown();
    $.ajax({
      data: $(this).serialize(),
      type: $(this).attr("method"),
      url: $(this).attr("action"),
      success: function(response) {
        if (response.status == true) {
          $("#reset-password-response-success").slideDown();
          $("#loader").slideUp(3000, function() {
            $("#reset-password-response-success").slideUp();
            location.href = "./home";
          });
        } else {
          console.log(JSON.stringify(response));
          $("#reset-password-response").slideDown();
          $("#loader").slideUp();
        }
      },
      error: function(xhr, desc, err) {
        console.log(err + " " + desc);
        $("#reset-password-response").slideDown();
        $("#loader").hide();
      },
    });
    return false;
  });
});
