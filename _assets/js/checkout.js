import jQuery from "jquery";
import { setCartItems, displayCartQuantity } from "./main.js";

window.$ = window.jQuery = jQuery;

jQuery(document).ready(function($) {
  checkUser();
  if (!checkCart()) {
    location.href = "./home";
  }

  displayOrderSummary();

  $("#payment-form").submit(function() {
    $("#loader").slideDown();

    const paymentOption = $("[name=payment-option]:checked").val();
    var cart = JSON.parse(sessionStorage.getItem("cart")); //cart = '"' + cart + '"';
    var products = cart["products"];

    $.ajax({
      data: $(this).serialize() + "&cart=" + JSON.stringify(products),
      type: $(this).attr("method"),
      url: $(this).attr("action"),
      success: function(response) {
        if (response.status == true) {
          $("#loader").slideUp(4000, function() {
            $("#payment-form").hide();
            if (paymentOption == "now") {
              $("#payment-response-success").slideDown();
              $("#payment-btn").attr("href", response.data);
              $("#payment-btn").removeClass("hidden");
            } else {
              $("#payment-response-success-invoice").slideDown();
            }
            sessionStorage.removeItem("cart");
            displayOrderSummary();
            displayCartQuantity();
          });
        } else {
          $("#payment-response").slideDown();
          $("#loader").slideUp(4000, function() {
            $("#loader").slideUp();
          });
        }
      },
      error: function(xhr, desc, err) {
        $("#payment-response").text(err);
        $("#payment-response").slideDown();
        $("#loader").slideUp();
      },
    });
    $("#payment-response-success").slideUp();
    $("#payment-response").slideUp();
    return false;
  });

  /* SHIPPING ADDRESS TOGGLE 
    ============================ */
  $(".cb-another-address input:checkbox").change(function() {
    if ($(this).is(":checked")) {
      $(".another-address-content").slideDown();
      $("#delivery-full-name").prop("required", true);
      $("#delivery-address").prop("required", true);
      $("#delivery-city").prop("required", true);
      $("#delivery-postcode").prop("required", true);
      $("#delivery-country").prop("required", true);
      $("#delivery-phone-number").prop("required", true);
    } else {
      $(".another-address-content").slideUp();
      $("#delivery-full-name").prop("required", false);
      $("#delivery-address").prop("required", false);
      $("#delivery-city").prop("required", false);
      $("#delivery-postcode").prop("required", false);
      $("#delivery-country").prop("required", false);
      $("#delivery-phone-number").prop("required", false);
    }
  });

  /* COMPANY TOGGLE 
      ============================ */
  $(".cb-company input:checkbox").change(function() {
    if ($(this).is(":checked")) {
      $(".company-content").slideDown();
      $("#company-name").prop("required", true);
    } else {
      $(".company-content").slideUp();
      $("#company-name").prop("required", false);
    }
  });
});

function checkCart() {
  const cart = JSON.parse(sessionStorage.getItem("cart"));
  if (cart == null || cart == "undefined") {
    return false;
  }

  const products = cart["products"];
  if (products == null || products == "undefined" || products.length == 0) {
    return false;
  }

  return true;
}

function getCartItems() {
  var cart = JSON.parse(sessionStorage.getItem("cart"));
  if (cart == null || cart == "undefined") {
    cart = { products: [] };
  }
  return cart["products"];
}

function displayOrderSummary() {
  const orderSummaryElement = document.getElementById("order-summary-items");
  if (orderSummaryElement == null) {
    return;
  }

  orderSummaryElement.innerHTML = "";
  const items = getCartItems();
  for (let i = 0; i < items.length; i++) {
    const item = document.createElement("div");
    item.className = "item flex items-center";
    item.id = "item-" + i;
    item.innerHTML = `
      <img src="${items[i].image}" class="m-2" alt="Product">
      <div class="content">
          <h2 class="text-lg">${items[i].name}</h2>
          <p class="text-sm text-black-200">Quantity: ${items[i].quantity}</p>
          <p class="text-sm text-black-200">Price: ${items[i].price} CHF</p>
      </div>

      <div id="remove-${i}" class="remove-product">
          <?xml version="1.0" encoding="iso-8859-1"?> <!-- Generator: Adobe Illustrator 19.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0) --> <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512.001 512.001" style="enable-background:new 0 0 512.001 512.001;" xml:space="preserve"> <g> <g> <path d="M284.286,256.002L506.143,34.144c7.811-7.811,7.811-20.475,0-28.285c-7.811-7.81-20.475-7.811-28.285,0L256,227.717 L34.143,5.859c-7.811-7.811-20.475-7.811-28.285,0c-7.81,7.811-7.811,20.475,0,28.285l221.857,221.857L5.858,477.859 c-7.811,7.811-7.811,20.475,0,28.285c3.905,3.905,9.024,5.857,14.143,5.857c5.119,0,10.237-1.952,14.143-5.857L256,284.287 l221.857,221.857c3.905,3.905,9.024,5.857,14.143,5.857s10.237-1.952,14.143-5.857c7.811-7.811,7.811-20.475,0-28.285 L284.286,256.002z"/></svg> 
      </div>
    `;

    $(orderSummaryElement).append(item);

    $(`#remove-${i}`).click(function() {
      $(`#item-${i}`).slideUp(500, function() {
        $(`#item-${i}`).remove();
        items.splice(i, 1);
        setCartItems(items);
        displayCartQuantity();
        displayOrderSummary();
        displayTotalValue();
      });
    });
    displayTotalValue();
  }
}

function displayTotalValue() {
  var totalValue = 0;
  const items = getCartItems();
  items.forEach((item) => {
    totalValue += item.price * item.quantity;
  });
  totalValue = totalValue.toFixed(2);
  $("#total-value").text(totalValue);
  sessionStorage.setItem("total", totalValue);
}

function checkUser() {
  var url = $(location).attr("href"),
    parts = url.split("/"),
    last_part = parts[parts.length - 1];
  if (userLoggedIn() && userIsCompany() && last_part != "checkout-company") {
    location.href = "./checkout-company";
  } else if (userLoggedIn() && !userIsCompany() && last_part != "checkout") {
    location.href = "./checkout";
  } else if (!userLoggedIn() && last_part != "checkout-guest") {
    location.href = "./checkout-guest";
  }
}

function userLoggedIn() {
  const user = sessionStorage.getItem("user");
  if (user == null || user == "undefined") {
    return false;
  }
  return true;
}

function userIsCompany() {
  const user = JSON.parse(sessionStorage.getItem("user"));
  if (user["company-name"]) {
    return true;
  }
  return false;
}
