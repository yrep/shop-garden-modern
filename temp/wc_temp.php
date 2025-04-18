private static function checkout() {
		// Show non-cart errors.
		do_action( 'woocommerce_before_checkout_form_cart_notices' );

		// Check cart has contents.
		if ( WC()->cart->is_empty() && ! is_customize_preview() && apply_filters( 'woocommerce_checkout_redirect_empty_cart', true ) ) {
			return;
		}

		// Check cart contents for errors.
		do_action( 'woocommerce_check_cart_items' );

		// Calc totals.
		WC()->cart->calculate_totals();

		// Get checkout object.
		$checkout = WC()->checkout();

		if ( empty( $_POST ) && wc_notice_count( 'error' ) > 0 ) { // WPCS: input var ok, CSRF ok.

			wc_get_template( 'checkout/cart-errors.php', array( 'checkout' => $checkout ) );
			wc_clear_notices();

		} else {

			$non_js_checkout = ! empty( $_POST['woocommerce_checkout_update_totals'] ); // WPCS: input var ok, CSRF ok.

			if ( wc_notice_count( 'error' ) === 0 && $non_js_checkout ) {
				wc_add_notice( __( 'The order totals have been updated. Please confirm your order by pressing the "Place order" button at the bottom of the page.', 'woocommerce' ) );
			}

			wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout ) );

		}
	}
	
	
	
<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="Подтвердить заказ" data-value="Подтвердить заказ">Подтвердить заказ</button>

public static function checkout_action() {
		if ( isset( $_POST['woocommerce_checkout_place_order'] ) || isset( $_POST['woocommerce_checkout_update_totals'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wc_nocache_headers();

			if ( WC()->cart->is_empty() ) {
				wp_safe_redirect( wc_get_cart_url() );
				exit;
			}

			wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );

			WC()->checkout()->process_checkout();
		}
	}
	
	
<input type="text" class="input-text " name="billing_first_name" id="billing_first_name" placeholder="" value="ewre" aria-required="true" autocomplete="given-name">
<input type="text" class="input-text " name="billing_last_name" id="billing_last_name" placeholder="" value="werwe" aria-required="true" autocomplete="family-name">
<span class="select2-selection__rendered" aria-required="true" id="select2-billing_country-container" role="textbox" aria-readonly="true" title="Россия">Россия</span>
<input type="text" class="input-text " name="billing_address_1" id="billing_address_1" placeholder="Номер дома и название улицы" value="dfg 34" aria-required="true" autocomplete="address-line1" data-placeholder="Номер дома и название улицы">



public function process_checkout() {
		try {
			$nonce_value    = wc_get_var( $_REQUEST['woocommerce-process-checkout-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // phpcs:ignore
			$expiry_message = sprintf(
				/* translators: %s: shop cart url */
				__( 'Sorry, your session has expired. <a href="%s" class="wc-backward">Return to shop</a>', 'woocommerce' ),
				esc_url( wc_get_page_permalink( 'shop' ) )
			);

			if ( empty( $nonce_value ) || ! wp_verify_nonce( $nonce_value, 'woocommerce-process_checkout' ) ) {
				// If the cart is empty, the nonce check failed because of session expiry.
				if ( WC()->cart->is_empty() ) {
					throw new Exception( $expiry_message );
				}

				WC()->session->set( 'refresh_totals', true );
				throw new Exception( __( 'We were unable to process your order, please try again.', 'woocommerce' ) );
			}

			wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );
			wc_set_time_limit( 0 );

			do_action( 'woocommerce_before_checkout_process' );

			if ( WC()->cart->is_empty() ) {
				throw new Exception( $expiry_message );
			}

			do_action( 'woocommerce_checkout_process' );

			$errors      = new WP_Error();
			$posted_data = $this->get_posted_data();

			// Update session for customer and totals.
			$this->update_session( $posted_data );

			// Validate posted data and cart items before proceeding.
			$this->validate_checkout( $posted_data, $errors );

			foreach ( $errors->errors as $code => $messages ) {
				$data = $errors->get_error_data( $code );
				foreach ( $messages as $message ) {
					wc_add_notice( $message, 'error', $data );
				}
			}

			if ( empty( $posted_data['woocommerce_checkout_update_totals'] ) && 0 === wc_notice_count( 'error' ) ) {
				$this->process_customer( $posted_data );
				$order_id = $this->create_order( $posted_data );
				$order    = wc_get_order( $order_id );

				if ( is_wp_error( $order_id ) ) {
					throw new Exception( $order_id->get_error_message() );
				}

				if ( ! $order ) {
					throw new Exception( __( 'Unable to create order.', 'woocommerce' ) );
				}

				do_action( 'woocommerce_checkout_order_processed', $order_id, $posted_data, $order );

				/**
				 * Note that woocommerce_cart_needs_payment is only used in
				 * WC_Checkout::process_checkout() to keep backwards compatibility.
				 * Use woocommerce_order_needs_payment instead.
				 *
				 * Note that at this point you can't rely on the Cart Object anymore,
				 * since it could be empty see:
				 * https://github.com/woocommerce/woocommerce/issues/24631
				 */

				if ( apply_filters( 'woocommerce_cart_needs_payment', $order->needs_payment(), WC()->cart ) ) {
					$this->process_order_payment( $order_id, $posted_data['payment_method'] );
				} else {
					$this->process_order_without_payment( $order_id );
				}
			}
		} catch ( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
		}
		$this->send_ajax_failure_response();
	}