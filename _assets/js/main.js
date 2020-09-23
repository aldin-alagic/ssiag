import jQuery from "jquery";
import "slick-carousel";

window.$ = window.jQuery = jQuery;

jQuery(document).ready(function($) {
  displayLoginStatus();
  setCart();
  displayCartQuantity();

  /* LANGUAGE SELECT */
  $("#select-language input").change(function() {
    var url = $(location).attr("href"),
      parts = url.split("/"),
      last_part = parts[parts.length - 1];
    if ($("#language-deutsch").is(":checked")) {
      location.href = "/icebein/" + last_part;
    } else {
      location.href = "/icebein/en/" + last_part;
    }
  });

  /* LOGOUT
      ================ */

  $("#logout-btn").click(function() {
    sessionStorage.removeItem("user");
    displayLoginStatus();
  });

  /* CART POPUP CLOSE 
      ================ */
  let closeCartPopupBox = document.getElementById("close-popup");
  let cartPopupBox = document.getElementById("cart-popup-box");

  $(closeCartPopupBox).click(function() {
    cartPopupBox.classList.remove("active");
  });

  /* OPENCART POPUP FROM MENU
      ============================= */
  let cartMenuItem = document.getElementById("cart-menu-item");

  $(cartMenuItem).click(function() {
    cartPopupBox.classList.add("active");
    displayCartItems();
  });

  /* BURGER MENU
      ================ */
  $(".mobile-menu-toggle-btn").click(function() {
    $(this).toggleClass("open");
    $(".menu-header-menu-container").toggleClass("active");
    $("body").toggleClass("disable-scroll");
  });

  /* SLICK SLIDER
    ================= */
  $(".slider-for").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: true,
    asNavFor: ".slider-nav",
    autoplay: true,
  });

  $(".slider-nav").slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    asNavFor: ".slider-for",
    dots: false,
    centerMode: true,
    focusOnSelect: true,
  });
});

/* Display Checkout Cart */

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

function setCart() {
  var cart = sessionStorage.getItem("cart");
  if (cart == null || cart == "undefined") {
    cart = { products: [] };
    sessionStorage.setItem("cart", JSON.stringify(cart));
  }
}

function getCartItems() {
  var cart = JSON.parse(sessionStorage.getItem("cart"));
  if (cart == null || cart == "undefined") {
    cart = { products: [] };
  }
  return cart["products"];
}

export function setCartItems(items) {
  const cart = JSON.parse(sessionStorage.getItem("cart"));
  cart["products"] = items;
  sessionStorage.setItem("cart", JSON.stringify(cart));
  console.log("New cart: " + JSON.stringify(cart));
}

export function displayCartQuantity() {
  var quantity = getCartQuantity();
  var textQuantity = "[" + quantity + "]";
  $("#cart-quantity").text(textQuantity);
  $("#cart-title").text(quantity);
}

export function displayCartItems() {
  var cartItemsElement = document.getElementById("cart-item-list");
  cartItemsElement.innerHTML = "";

  const items = getCartItems();
  for (let i = 0; i < items.length; i++) {
    const item = document.createElement("div");
    item.className = "item";
    item.id = "item-" + i;
    item.innerHTML = `
      <img src="${items[i].image}" class="m-2" alt="Product" />
      <div class="">
        <h2 class="text-lg">${items[i].name}</h2>
        <p class="text-sm text-black-200">Quantity: ${items[i].quantity}</p>
        <p class="text-sm text-black-200">Price: ${items[i].price} CHF</p>
      </div>
      <div id="remove-${i}" class="remove-product">
        <?xml version="1.0" encoding="iso-8859-1"?>
        <!-- Generator: Adobe Illustrator 19.0.0, SVG Export Plug-In . SVG Version: 6.00 Build 0) -->
        <svg
          version="1.1"
          id="Capa_1"
          xmlns="http://www.w3.org/2000/svg"
          xmlns:xlink="http://www.w3.org/1999/xlink"
          x="0px"
          y="0px"
          viewBox="0 0 512.001 512.001"
          style="enable-background:new 0 0 512.001 512.001;"
          xml:space="preserve"
        >
          <g>
            <path
              d="M284.286,256.002L506.143,34.144c7.811-7.811,7.811-20.475,0-28.285c-7.811-7.81-20.475-7.811-28.285,0L256,227.717 L34.143,5.859c-7.811-7.811-20.475-7.811-28.285,0c-7.81,7.811-7.811,20.475,0,28.285l221.857,221.857L5.858,477.859 c-7.811,7.811-7.811,20.475,0,28.285c3.905,3.905,9.024,5.857,14.143,5.857c5.119,0,10.237-1.952,14.143-5.857L256,284.287 l221.857,221.857c3.905,3.905,9.024,5.857,14.143,5.857s10.237-1.952,14.143-5.857c7.811-7.811,7.811-20.475,0-28.285 L284.286,256.002z"
            />
          </g>
        </svg>
      </div>`;
    $(cartItemsElement).append(item);

    $(`#remove-${i}`).click(function() {
      $(`#item-${i}`).slideUp(500, function() {
        items.splice(i, 1);
        setCartItems(items);
        displayCartQuantity();
        displayCartItems();
        displayOrderSummary();
      });
    });
  }

  let checkoutButton = "";
  if (userLoggedIn() && userIsCompany()) {
    checkoutButton = `
    <a
      href="./checkout-company"
      class="text-center mt-10 font-oswald bg-green-primary text-white w-full block py-3 hover:bg-orange-primary px-10"
    >PROCEED TO CHECKOUT</a>`;
  } else if (userLoggedIn()) {
    checkoutButton = `
    <a
      href="./checkout"
      class="text-center mt-10 font-oswald bg-green-primary text-white w-full block py-3 hover:bg-orange-primary px-10"
    >PROCEED TO CHECKOUT</a>`;
  } else {
    checkoutButton = `
    <a
      href="./checkout-guest"
      class="text-center mt-10 font-oswald bg-green-primary text-white w-full block py-3 hover:bg-orange-primary px-10"
    >PROCEED TO CHECKOUT</a>`;
  }
  $(cartItemsElement).append(checkoutButton);
}

function getCartQuantity() {
  return getCartItems().length;
}

function displayLoginStatus() {
  if (userLoggedIn()) {
    const user = JSON.parse(sessionStorage.getItem("user"));
    const userName = user["name"];
    $("#logged-user-name").text(userName);
    $("#logged-user").show("fast");
    $("#logout-btn").show("fast");
    $("#login-btn").hide("fast");
    $("#register-btn").hide("fast");
    return;
  }
  $("#logout-btn").hide("fast");
  $("#logged-user").hide("fast");
  $("#logged-user-name").text("-");
  $("#login-btn").show("fast");
  $("#register-btn").show("fast");
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
