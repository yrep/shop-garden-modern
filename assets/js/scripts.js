var gmsOpenCart;

document.addEventListener('DOMContentLoaded', function () {
    
    const gmsCartButton = document.querySelector('.gms-cart-icon');
    const cartCountElement = document.querySelector('.cart-count');
    const buttonCheckInterval = null;
    let isHandlersBound = false;
    
    document.body.addEventListener('click', function(event) {
        const target = event.target.closest('a.added_to_cart.wc-forward');
        //console.dir(target);
        if (target) {
            event.preventDefault();
            modalCheckout.style.display = "block";
            document.body.classList.add("modal-open");
            loadCartAndCheckout();
        }
    });

    gmsCartButton.addEventListener('click', function (event) {
        event.preventDefault();
        modalCheckout.style.display = "block";
        document.body.classList.add("modal-open");
        loadCartAndCheckout();
    });

    gmsCartButton.addEventListener('touchstart', function (event) {
        modalCheckout.style.display = "block";
        document.body.classList.add("modal-open");
        loadCartAndCheckout();
    }, { passive: true });

    const modalCheckout = document.querySelector('#gms-modal-checkout');
    const modalCloseButton = document.querySelector('.gms-close-modal-btn');
    
    //Disable place order button
    
    function checkButtonAndDisable() {
        const placeOrderButton = document.getElementById('place_order');
        if (placeOrderButton) {
            placeOrderButton.disabled = true;
            clearInterval(buttonCheckInterval);
        }
    }

    function resetColumns() {
        const col1 = document.querySelector('.col-1');
        const col2 = document.querySelector('.col-2');
        const col2Set = document.querySelector('.col2-set');
        const modalCustomerDetails = document.querySelector('#customer_details');
        const modalOrderReviewHeading = document.querySelector('#order_review_heading');
        const firstName = document.querySelector('#billing_first_name_field');
        const lastName = document.querySelector('#billing_last_name_field');
        const orderReview = document.querySelector('#order_review');

        if (modalCustomerDetails && col1 && col2) {
            col1.style.float = 'none';
            col2.style.float = 'none';
            modalOrderReviewHeading.style.display = 'none';
            col2Set.style.float = 'none';
            firstName.style.float = 'none';
            lastName.style.float = 'none';
            col1.style.width = '100%';
            col2.style.width = '100%';
            modalOrderReviewHeading.style.width = '100%';
            col2Set.style.width = '100%';
            firstName.style.width = '100%';
            lastName.style.width = '100%';
            modalCustomerDetails.style.display = 'flex';
            modalCustomerDetails.style.flexDirection = 'column';
            modalCustomerDetails.style.float = 'none';
            modalCustomerDetails.style.width = '100%';
            col2Set.style.paddingRight = 0;
            orderReview.style.paddingLeft = 0;
        }
    }

    resetColumns();
    applyChanges();

    function applyChanges() {
        const observer = new MutationObserver(function (mutationsList, observer) {
    
            const paymentMethods = document.querySelector('.payment_methods');
    
            if (paymentMethods && !paymentMethods.dataset.updated){
                paymentMethods.style.display = 'none';
                console.dir(paymentMethods);
                paymentMethods.dataset.updated = true;
            }
    
            const wcOrderTableHeading = document.querySelector('#order_review_heading');
            if (wcOrderTableHeading && !wcOrderTableHeading.dataset.updated) {
                wcOrderTableHeading.style.display = 'none';
                wcOrderTableHeading.dataset.updated = true;
            }
    
            const wcOrderTable = document.querySelector('.woocommerce-checkout-review-order-table');
            if (wcOrderTable && !wcOrderTable.dataset.updated) {
                wcOrderTable.style.display = 'none';
                wcOrderTable.dataset.updated = true;
            }
    
            const customerDetailsCol2 = document.querySelector('#customer_details .col-2');
            if (customerDetailsCol2 && !customerDetailsCol2.dataset.updated) {
                resetColumns();
                customerDetailsCol2.dataset.updated = true;
            }
            
            if (paymentMethods && wcOrderTable && customerDetailsCol2 && wcOrderTableHeading) {
                observer.disconnect();
            }
    
        });
    
        observer.observe(document.body, { childList: true, subtree: true });
    }
    
    function removeAllEventListeners(element) {
        const clone = element.cloneNode(true);
        element.parentNode.replaceChild(clone, element);
        return clone;
    }

    modalCloseButton.onclick = function () {
        modalCheckout.style.display = "none";
        document.body.classList.remove("modal-open");
    };

    window.onclick = function (event) {
        if (event.target == modalCheckout) {
            modalCheckout.style.display = "none";
            document.body.classList.remove("modal-open");
        }
    };

    function loadCartAndCheckout() {
        const container = document.getElementById("gms-cart-checkout-container");
        
        const data = new FormData();
        data.append('action', 'gms_get_cart_checkout_content');

        fetch(ajaxurl, {
            method: 'POST',
            body: new URLSearchParams(data),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = data.data.content;
                    
                    bindEventHandlers();
                    
                    const placeOrderButton = container.querySelector('#place_order');
                    if (placeOrderButton) {
                        placeOrderButton.disabled = true;
                    } else {
                        buttonCheckInterval = setInterval(checkButtonAndDisable, 500);
                    }

                    checkForm();
                } else {
                    container.innerHTML = '<p>Произошла ошибка при загрузке данных.</p>';
                }
            }).then(() => {
                resetColumns();
                applyChanges();
            })
            .catch(error => {
                container.innerHTML = '<p>Произошла ошибка при загрузке данных.</p>';
            });
            
    }


    function bindEventHandlers() {
        if (isHandlersBound) return; // Если уже были добавлены — выходим
        isHandlersBound = true;

        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('gms-decrease-quantity')) {
                handleQuantityChange(event, -1);
            } else if (event.target.classList.contains('gms-increase-quantity')) {
                handleQuantityChange(event, 1);
            } else if (event.target.classList.contains('gms-remove-item')) {
                handleRemoveItem(event);
            }
        });

        document.addEventListener('input', function (event) {
            if (event.target.classList.contains('gms-item-quantity')) {
                handleManualQuantityChange(event);
            }
        });
    }


    function handleQuantityChange(event, delta) {
        const cartItem = event.target.closest('.cart-item');
        const quantityInput = cartItem.querySelector('.gms-item-quantity');
        const itemTotal = cartItem.querySelector('.gms-item-total');
        const cartItemKey = cartItem.dataset.cart_item_key;
        const productId = cartItem.dataset.product_id;
        let newQuantity = parseInt(quantityInput.value) + delta;

        const minimalQuantity = 1;

        if (newQuantity < minimalQuantity) newQuantity = minimalQuantity;

        updateCartQuantity(cartItemKey, newQuantity, productId, quantityInput, itemTotal);
    }


    function handleManualQuantityChange(event) {
        const quantityInput = event.target;
        const cartItem = quantityInput.closest('.cart-item');
        const cartItemKey = cartItem.dataset.cart_item_key;
        const productId = cartItem.dataset.product_id;
        const newQuantity = parseInt(quantityInput.value, 10);
        const itemTotal = cartItem.querySelector('.gms-item-total');

        const minimalQuantity = 1;

        if (newQuantity < minimalQuantity) {
            quantityInput.value = minimalQuantity;
            return;
        }

        updateCartQuantity(cartItemKey, newQuantity, productId, quantityInput, itemTotal);
    }


    function handleRemoveItem(event) {
        const cartItem = event.target.closest('.cart-item');
        const cartItemKey = cartItem.dataset.cart_item_key;

        removeItemFromCart(cartItemKey, cartItem);
    }


    function updateCartQuantity(cartItemKey, quantity, productId, quantityInput, itemTotal) {
        const data = new FormData();
        data.append('action', 'gms_update_cart_quantity');
        data.append('cart_item_key', cartItemKey);
        data.append('quantity', quantity);
        data.append('product_id', productId);

        fetch(ajaxurl, {
            method: 'POST',
            body: data
        })
            .then(response => response.json())
            .then(data => {

                if (data.success) {
                    cartErrorMessage = document.querySelector("#cart-error-message");
                    if(cartErrorMessage){
                        cartErrorMessage.textContent = '';
                        cartErrorMessage.classList.remove = 'show';
                    }
                    
                    console.log('Data for update');
                    console.dir(data);
                    quantityInput.value = data.data.new_quantity;
                    itemTotal.textContent = data.data.cart_data_with_tax[cartItemKey].line_total + '₽';
                    updateTotals(data);
                } else {
                    cartErrorMessage = document.querySelector("#cart-error-message");
                    if(cartErrorMessage){
                        cartErrorMessage.textContent = data.data.message;
                        cartErrorMessage.classList.add = 'show';
                    }
                    console.log(data.data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка AJAX:', error);
            });
    }
    function removeItemFromCart(cartItemKey, cartItemElement) {
        const data = new FormData();
        data.append('action', 'gms_remove_from_cart');
        data.append('cart_item_key', cartItemKey);
        fetch(ajaxurl, {
            method: 'POST',
            body: data,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartItemElement.remove();
                    
                    updateTotals(data);

                    if (data.cart_html) {
                        const container = document.getElementById("gms-cart-checkout-container");
                        container.innerHTML = data.cart_html;
                        bindEventHandlers();
                    }
                } else {
                    console.log('Ошибка при удалении товара:', data.message);
                }
            })
            .catch(error => {
                console.error('Ошибка AJAX:', error);
            });
    }

    function updateTotals(data){
        
        const count = document.querySelector('#checkout-items-count > b');
        const totalElement = document.querySelector('#checkout-total > b');
        const gmsCartIconCountMain = document.querySelector('#main-header .header-cart-total');
        const gmsCartIconCountMobile = document.querySelector('#mobile-header .header-cart-total');
        const gmsTotalBottom = document.querySelector('#gms-total-bottom-summ');

        if(gmsCartIconCountMain && gmsCartIconCountMobile){
            gmsCartIconCountMain.textContent = data.data.totals.count;
            gmsCartIconCountMobile.textContent = data.data.totals.count;

        }

        if (count){
            count.textContent = data.data.totals.count;
        }

        if (totalElement && gmsTotalBottom) {
            totalElement.innerHTML = data.data.totals.total;
            gmsTotalBottom.innerHTML = data.data.totals.total;
        }

    }

    gmsOpenCart = function gmsOpenCartCheckout(){
        modalCheckout.style.display = "block";
        document.body.classList.add("modal-open");
        loadCartAndCheckout();
    }


    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    function checkForm() {
    
        const form = document.querySelector('form.woocommerce-checkout');
        const placeOrderButton = document.getElementById('place_order');
        const errorMessage = document.getElementById('error-message');

        if (form && placeOrderButton) {

            const requiredFields = [
                form.querySelector('#billing_first_name'),
                form.querySelector('#billing_last_name'),
                form.querySelector('#billing_phone'),
                form.querySelector('#billing_email'),
                form.querySelector('#order_comments'),
                form.querySelector('#terms'),
            ];

            

            let isFormValid = true;

            requiredFields.forEach(field => {
                
                if (!field) {
                    return;
                };

                if (field.type === 'checkbox') {
                    if (!field.checked) {
                        isFormValid = false;
                    }
                } else {
                    if (!field.value.trim()) {
                        isFormValid = false;
                    }
                }
            });

            placeOrderButton.disabled = !isFormValid;
            if(!isFormValid && errorMessage){
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        }
    }

    document.addEventListener('input', debounce(function(event) {
        const target = event.target;

        if (target.closest('form.woocommerce-checkout')) {
            checkForm();
        }
    }, 500));


    document.addEventListener('change', function(event) {
        const target = event.target;

        if (target.closest('form.woocommerce-checkout') && target.type === 'checkbox') {
            checkForm();
        }
    });

    bindEventHandlers();
});