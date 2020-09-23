import jQuery from "jquery";
window.$ = window.jQuery = jQuery;

jQuery(document).ready(function($) {
  const token = getToken();
  $("#token").val(token);
  console.log("Token:" + token);

  $("#reset-password-form").submit(function() {
    $("#reset-password-response").slideUp();
    $("#loader").slideDown();

    if ($("#password").val() != $("#password-confirm").val()) {
      $("#reset-password-response").text(
        "Passwords do not match! Please try again."
      );
      $("#reset-password-response").slideDown();
      return false;
    }

    $.ajax({
      data: $(this).serialize(),
      type: $(this).attr("method"),
      url: $(this).attr("action"),
      success: function(response) {
        if (response.status == true) {
          sessionStorage.removeItem("user");
          $("#reset-password-response-success").slideDown();
          $("#loader").slideUp(3000, function() {
            $("#reset-password-response-success").slideUp();
            location.href = "./login";
          });
        } else {
          $("#reset-password-response").text(
            "The link is invalid or expired. Please try again."
          );
          $("#reset-password-response").slideDown();
          $("#loader").slideUp();
        }
      },
      error: function(xhr, desc, err) {
        $("#reset-password-response").text(err);
        $("#loader").hide();
      },
    });
    return false;
  });
});

function getToken() {
  const parameterName = "key";
  var result = null;
  var tmp = [];
  location.search
    .substr(1)
    .split("&")
    .forEach(function(item) {
      tmp = item.split("=");
      if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    });
  return result;
}
