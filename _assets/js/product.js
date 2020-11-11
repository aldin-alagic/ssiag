import jQuery from "jquery";
window.$ = window.jQuery = jQuery;

$(document).ready(function($) {
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

  getProductStock(id);
  $("#select-size").change(function(event) {
    getProductStock(id);
  });
  $("#select-length").change(function(event) {
    getProductStock(id);
  });
  $("#input-quantity").change(function(event) {
    getProductStock(id);
  });

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

function getProductStock(id) {
  const quantity = parseInt($("#input-quantity").val());
  const selectedSize = $("#select-size").val(); //1,2,3
  const selectedLength = $("#select-length").val(); // N, L
  const sizeLength = selectedLength + selectedSize;
  let stock = -5;

  $.ajax({
    url: "https://ssiag.com/api/product.php",
    data: { product_id: id, size_length: sizeLength },
    dataType: "json",
    type: "post",
    success: function(response) {
      $("#stock").text(response.data);
    },
    error: function(e) {
      alert(JSON.stringify(e));
    },
  });

  if (stock < quantity || quantity <= 0) {
    $("#add-btn").attr("disabled", true);
    $("#add-btn").hide();
  } else {
    $("#add-btn").attr("disabled", false);
    $("#add-btn").show();
  }
}
