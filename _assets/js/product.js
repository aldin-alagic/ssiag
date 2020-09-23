import jQuery from "jquery";
window.$ = window.jQuery = jQuery;

jQuery(document).ready(function($) {
  $("#select-size").change(function() {
    const selectedSize = $("#select-size").val();
    $("#select-length").empty();
    if (selectedSize == "1") {
      $("#select-length").append(new Option("N (Normal)", "N", null, true));
    } else {
      $("#select-length").append(new Option("N (Normal)", "N", null, true));
      $("#select-length").append(new Option("L (Large)", "L"));
    }
  });

  const id = $("#id")
    .text()
    .trim();
  const name = $("#name")
    .text()
    .trim();
  const image = $("#image")
    .text()
    .trim();
  const price = $("#price")
    .text()
    .trim();

  // ADD ITEM TO CART
  let productForm = "#product-form";
  $(productForm).submit(function() {
    const quantity = $("#input-quantity").val();
    const size = $("#select-size").val();
    const length = $("#select-length").val();
    const product = { id, name, price, quantity, size, length, image };
    addItemCart(product);
    location.href = "./home";
  });
});

function addItemCart(item) {
  var cart = JSON.parse(sessionStorage.getItem("cart"));
  cart["products"].push(item);
  sessionStorage.setItem("cart", JSON.stringify(cart));
}
