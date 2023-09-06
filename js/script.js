

function modifyQuantity(value, event) {
    const btnClicked = event.target; // The button that was clicked
    let quantityInput = btnClicked.closest('.quantity-and-cart').querySelector(".quantity");
    let currentVal = parseInt(quantityInput.value, 10);
    currentVal += value;

    // Make sure it doesn't go below the minimum value
    if (currentVal < 1) currentVal = 1;

    quantityInput.value = currentVal;
}






	



