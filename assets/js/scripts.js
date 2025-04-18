var gmsOpenCart;

document.addEventListener('DOMContentLoaded', function () {
    
    



    // document.addEventListener('click', logEvent);
    // document.addEventListener('keydown', logEvent);
    // document.addEventListener('mouseover', logEvent);
    // document.addEventListener('touchstart', logEvent);
    // Добавляйте сюда другие события по необходимости

    // function logEvent(event) {
    //     event.preventDefault();
    //     event.target
    //     console.log('Событие:', event.type);
    //     console.log('Цель события:', event.target);
    //     console.log('Полная информация о событии:', event);
    //     console.dir(event);
    // }

/*
    const headerSection = document.querySelector('.site-header-main-section-right');

    console.dir(headerSection);

    if (headerSection) {
        var gmsCartButton = document.createElement('a');
        gmsCartButton.href = '#';
        gmsCartButton.className = 'gms-cart-icon';
        gmsCartButton.innerHTML = '<span>C</span>';
        headerSection.appendChild(gmsCartButton);
    }
*/
    const gmsCartButton = document.querySelector('.gms-cart-icon');
    const cartCountElement = document.querySelector('.cart-count');
    
    /*
    const nextKadenceElement = gmsCartButton.nextElementSibling;


    console.dir(nextKadenceElement);
    if (nextKadenceElement) {
        console.log('Элемент найден');
        nextKadenceElement.style.display = 'none';
        console.log('Отменили видимость');
    }
*/

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
        //event.preventDefault();
        modalCheckout.style.display = "block";
        document.body.classList.add("modal-open");
        loadCartAndCheckout();
    }, { passive: true });

    //const kadenceCartIcon = document.querySelector('.header-cart-button');
    const modalCheckout = document.querySelector('#gms-modal-checkout');
    const modalCloseButton = document.querySelector('.gms-close-modal-btn');
    
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
        //console.log('Columns reset');
    }

    resetColumns();
    applyChanges();

    function applyChanges() {
        const observer = new MutationObserver(function (mutationsList, observer) {
    
            // const tbankPayment = document.querySelector('.payment_method_tbank');
            // if (tbankPayment && !tbankPayment.dataset.updated) {
            //     tbankPayment.innerHTML = 'К оплате принимаются любые российские карты. Оплата производится через терминал Т-Банка, на который вы будете перенаправлены после подтверждения заказа.';
            //     tbankPayment.dataset.updated = 'true';
            // }
    
            const paymentMethods = document.querySelector('.payment_methods');
    
            if (paymentMethods && !paymentMethods.dataset.updated){
                paymentMethods.style.display = 'none';
                //console.log('Т-банк скрыт');
                console.dir(paymentMethods);
                paymentMethods.dataset.updated = true;
            }
    
            const wcOrderTableHeading = document.querySelector('#order_review_heading');
            if (wcOrderTableHeading && !wcOrderTableHeading.dataset.updated) {
                wcOrderTableHeading.style.display = 'none';
                //console.log('Таблица заказа скрыта');
                wcOrderTableHeading.dataset.updated = true;
            }
    
            const wcOrderTable = document.querySelector('.woocommerce-checkout-review-order-table');
            if (wcOrderTable && !wcOrderTable.dataset.updated) {
                wcOrderTable.style.display = 'none';
                //console.log('Таблица заказа скрыта');
                wcOrderTable.dataset.updated = true;
            }
    
            const customerDetailsCol2 = document.querySelector('#customer_details .col-2');
            if (customerDetailsCol2 && !customerDetailsCol2.dataset.updated) {
                resetColumns();
                //console.log('Колонки сброшены');
                customerDetailsCol2.dataset.updated = true;
            }
            
            /*
            const kadenceCartIcon = document.querySelector('a.header-cart-button');
            if (kadenceCartIcon && !kadenceCartIcon.dataset.updated) {
                kadenceCartIcon.addEventListener('touchstart', function (event) {
                    event.preventDefault();
                    //removeAllEventListeners(kadenceCartIcon);
                    console.log('kadenceCartIcon обноружена');
                    console.dir(kadenceCartIcon);
                    console.dir(event);
                    kadenceCartIcon.baseURI = "";
                    kadenceCartIcon.href = "";
                    modalCheckout.style.display = "block";
                    document.body.classList.add("modal-open");
                    loadCartAndCheckout();
                });
                kadenceCartIcon.addEventListener('click', function (event) {
                    event.preventDefault();
                    removeAllEventListeners(kadenceCartIcon);
                    console.log('kadenceCartIcon обноружена');
                    console.dir(kadenceCartIcon);
                    console.dir(event);
                    kadenceCartIcon.baseURI = "";
                    kadenceCartIcon.href = "";
                    modalCheckout.style.display = "block";
                    document.body.classList.add("modal-open");
                    loadCartAndCheckout();
                });
            }
            */
            if (paymentMethods && wcOrderTable && customerDetailsCol2 && wcOrderTableHeading) {
                observer.disconnect();
                //console.log('Observer отключён');
            }
    
        });
    
        observer.observe(document.body, { childList: true, subtree: true });
    }
    
    function removeAllEventListeners(element) {
        const clone = element.cloneNode(true);
        element.parentNode.replaceChild(clone, element);
        return clone;
    }
    // Открытие модального окна
    /*
    kadenceCartIcon.addEventListener('click', function (event) {
        event.preventDefault();
        modalCheckout.style.display = "block";
        document.body.classList.add("modal-open");
        loadCartAndCheckout(); // Загружаем корзину при открытии модального окна
    });
    */

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
        const placeOrderButton = document.getElementById('place_order');
        placeOrderButton.disabled = true;
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
                } else {
                    container.innerHTML = '<p>Произошла ошибка при загрузке данных.</p>';
                }
            }).then(() => {
                resetColumns();
                applyChanges();
            })
            .catch(error => {
                //console.error('Ошибка запроса:', error);
                container.innerHTML = '<p>Произошла ошибка при загрузке данных.</p>';
            });
            
    }


    function bindEventHandlers() {
        document.addEventListener('click', function (event) {
            if (event.target && event.target.classList.contains('gms-decrease-quantity')) {
                handleQuantityChange(event, -1);
            } else if (event.target && event.target.classList.contains('gms-increase-quantity')) {
                handleQuantityChange(event, 1);
            } else if (event.target && event.target.classList.contains('gms-remove-item')) {
                handleRemoveItem(event);
            }
        });

        
        document.addEventListener('input', function (event) {
            if (event.target && event.target.classList.contains('gms-item-quantity')) {
                //console.log('Input change');
                //console.dir(event);
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
                    console.log('Data for update');
                    console.dir(data);
                    quantityInput.value = data.data.new_quantity;
                    itemTotal.textContent = data.data.cart_data_with_tax[cartItemKey].line_total + '₽';
                    //itemTotal.textContent = data.data.cart_data[cartItemKey]['line_total'] + ' ₽';
                    //console.log('Updated data:');
                    //console.dir(data);
                    updateTotals(data);
                    
                    // Не работает тут
                    //const currencySymbol = bdiElement.querySelector('.woocommerce-Price-currencySymbol').outerHTML;
                    //wcPriceAmount.innerHTML = `${data.data.totals.subtotal}&nbsp;${currencySymbol}`;

                    //Если сервер возвращает обновлённые данные корзины, обновите DOM
                    
                    /*
                    if (data.cart_html) {
                        const container = document.getElementById("gms-cart-checkout-container");
                        const count = document.querySelector('#checkout-items-count > b');
                        console

                        container.innerHTML = data.cart_html;
                        count.textContent = data.data.cart_contents_count;


                        // const wcPriceAmount = document.querySelector('.woocommerce-Price-amount');
                        // const currencySymbol = bdiElement.querySelector('.woocommerce-Price-currencySymbol').outerHTML;
                        // wcPriceAmount.innerHTML = `${data.data.totals.subtotal}&nbsp;${currencySymbol}`;

                        bindEventHandlers(); // Перепривязываем обработчики
                    }
                    */
                } else {
                    console.log('Ошибка при обновлении количества:', data.message);
                }
                    
            })
            .catch(error => {
                console.error('Ошибка AJAX:', error);
            });
    }

    // Функция для удаления товара
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
            //console.log('Form and button found');

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




   /*
    document.body.addEventListener('submit', function(event) {
        
        if (event.target && event.target.matches('form.woocommerce-checkout')) {
            const form = event.target;

            const checkoutForm = document.querySelector('form.woocommerce-checkout');
            const errorMessage = document.querySelector('#error-message');  
            const firstName = document.querySelector('#billing_first_name');
            const lastName = document.querySelector('#billing_last_name');
            const phone = document.querySelector('#billing_phone');
            const email = document.querySelector('#billing_email');
            const comment = document.querySelector('#order_comments');
            const terms = document.querySelector('#terms');

            let isFormValid = true;

            inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.classList.remove('error');
            });

            errorMessage.style.display = 'none';

            if (!firstName.value.trim()){
                isFormValid = false;
                firstName.classList.add('error');
            }

            if (!lastName.value.trim()){
                isFormValid = false;
                lastName.classList.add('error');
            }

            if (!phone.value.trim()){
                isFormValid = false;
                phone.classList.add('error');
            }

            if (!email.value.trim()){
                isFormValid = false;
                email.classList.add('error');
            }

            if (!comment.value.trim()){
                isFormValid = false;
                comment.classList.add('error');
            }

            if (!terms.value){
                isFormValid = false;
                terms.classList.add('error');
            }

            /*
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isFormValid = false;
                    input.classList.add('error');
                }
            });
            
            
            if (!isFormValid) {
                event.preventDefault();
                errorMessage.style.display = 'block';
            }
        }
    });
*/

/*
    function updateCartCount() {
        const data = new FormData();
        data.append('action', 'gms_update_cart_count');
        
        fetch(ajaxurl, {
            method: 'POST',
            body: data,
        })
            .then(response => response.json())
            .then(data => {
                if (data.cart_count !== undefined) {

                    document.querySelector('.cart-count').textContent = data.cart_count;
                }
            })
            .catch(error => console.error('Ошибка обновления корзины:', error));
    }
*/


    // function updateCartCount() {
    //     fetch('/wp-admin/admin-ajax.php?action=gms_update_cart_count')
    //         .then(response => response.json())
    //         .then(data => {
    //             if (data.cart_count !== undefined) {
    //                 const cartCountElement = document.querySelector('.cart-count');
    //                 if (cartCountElement) {
    //                     cartCountElement.textContent = data.cart_count;
    //                 }
    //             }
    //         })
    //         .catch(error => console.error('Ошибка при обновлении корзины:', error));
    // }


    
    // updateCartCount();

    
    // document.body.addEventListener('added_to_cart', function () {
    //     updateCartCount();
    // });


    bindEventHandlers();
});