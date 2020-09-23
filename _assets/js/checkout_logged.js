import jQuery from "jquery";

window.$ = window.jQuery = jQuery;

jQuery(document).ready(function($) {
  const user = getUser();

  if (userIsCompany()) {
    $("#company-name").val(user["company-name"]);
    $("#company-name").prop("readonly", true);
  }

  $("#full-name").val(user["name"]);
  $("#full-name").prop("readonly", true);

  $("#email").val(user["email"]);
  $("#email").prop("readonly", true);

  $("#address").val(user["street-address"]);
  $("#address").prop("readonly", true);

  $("#city").val(user["city"]);
  $("#city").prop("readonly", true);

  $("#postcode").val(user["post-code"]);
  $("#postcode").prop("readonly", true);

  $("#phone-number").val(user["phone-number"]);
  $("#phone-number").prop("readonly", true);
});

function getUser() {
  const user = sessionStorage.getItem("user");
  return JSON.parse(user);
}

function userIsCompany() {
  const user = JSON.parse(sessionStorage.getItem("user"));
  if (user["company-name"]) {
    return true;
  }
  return false;
}
