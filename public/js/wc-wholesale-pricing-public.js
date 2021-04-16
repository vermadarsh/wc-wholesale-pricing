jQuery( document ).ready( function( $ ) {
	'use strict';

	var {
		ajaxurl,
		invalid_product_id,
		invalid_product_quantity,
		notification_error_header,
		notification_success_header,
		wcwp_ajax_nonce
	} = WCWP_Public_JS_Obj;

	/**
	 * Add wholesale product to cart.
	 */
	$( document ).on( 'click', '.wcwp-add-to-cart-wholesale-product', function( e ) {
		e.preventDefault();

		var this_btn   = $( this );
		var product_id = this_btn.parents( 'tr' ).data( 'id' );
		var quantity = $( 'input[name="product-' + product_id + '-quantity"]' ).val();
		var error_li   = '';

		if ( -1 === is_valid( product_id ) ) {
			error_li += '<li>' + invalid_product_id + '</li>';
		}

		if ( -1 === is_valid( quantity ) ) {
			error_li += '<li>' + invalid_product_quantity + '</li>';
		}

		// Show the error message.
		if ( '' !== error_li ) {
			var error = '<ol type="1" style="margin: 0 0 0 10px;">' + error_li + '</ol>';
			wcwp_show_notification( 'fa fa-warning', notification_error_header, error, 'error' );
			return false;
		}

		// Block the element.
		block_element( this_btn.parents( 'tr' ) );

		// Send AJAX to add these items to cart.
		var data = {
			action: 'add_to_cart',
			product_id: product_id,
			quantity: quantity,
			wcwp_ajax_nonce: wcwp_ajax_nonce
		};
		$.ajax( {
			dataType: 'JSON',
			url: ajaxurl,
			type: 'POST',
			data: data,
			success: function ( response ) {
				// Update the mini cart now.
				wcwp_update_mini_cart();

				unblock_element( this_btn.parents( 'tr' ) );

				// Check for invalid ajax response.
				if ( 0 === response ) {
					wcwp_show_notification( 'fa fa-warning', notification_error_header, invalid_ajax_response, 'error' );
					return false;
				}

				// Check for the nonce check failure.
				if ( -1 === response ) {
					wcwp_show_notification( 'fa fa-warning', notification_error_header, ajax_nonce_failure, 'error' );
					return false;
				}

				var {code, notification_message} = response.data;

				if ( 'wcwp-product-added-to-cart' === code ) {
					wcwp_show_notification( 'fa fa-check', notification_success_header, notification_message, 'success' );
					$( '.wcpe-modal' ).removeClass( 'open' );
				}
			},
		} );
	} );

	// Update the mini cart.
	function wcwp_update_mini_cart() {
		var data = {
			action: 'update_mini_cart',
			wcwp_ajax_nonce: wcwp_ajax_nonce,
		};
		$.ajax( {
			url: ajaxurl,
			type: 'POST',
			data: data,
			success: function ( response ) {
				// Check for invalid ajax response.
				if ( 0 === response ) {
					wcwp_show_notification( 'fa fa-warning', notification_error_header, invalid_ajax_response, 'error' );
					return false;
				}

				// Check for the nonce check failure.
				if ( -1 === response ) {
					wcwp_show_notification( 'fa fa-warning', notification_error_header, ajax_nonce_failure, 'error' );
					return false;
				}

				$( '.widget_shopping_cart_content' ).html( response );
			},
		} );
	}

	/**
	 * Close the notification.
	 */
	$( document ).on( 'click', '.wcwp_notification_close', function() {
		wcwp_hide_notification();
	} );

	/**
	 * Check if a number is valid.
	 * 
	 * @param {number} data 
	 */
	function is_valid( data ) {
		if ( '' === data || undefined === data || isNaN( data ) || 0 === data ) {
			return -1;
		} else {
			return 1;
		}
	}

	/**
	 * Block element.
	 *
	 * @param {string} element 
	 */
	function block_element( element ) {
		element.addClass( 'non-clickable' );
	}

	/**
	 * Unblock element.
	 *
	 * @param {string} element 
	 */
	function unblock_element( element ) {
		element.removeClass( 'non-clickable' );
	}

	/**
	 * Function defined to show the notification.
	 *
	 * @param {string} icon_class
	 * @param {string} header_text
	 * @param {string} message
	 * @param {string} success_or_error
	 */
	function wcwp_show_notification( icon_class, header_text, message, success_or_error ) {
		$('.wcwp_notification_popup .wcwp_notification_icon i').removeClass().addClass( icon_class );
		$('.wcwp_notification_popup .wcwp_notification_message h3').html( header_text );
		$('.wcwp_notification_popup .wcwp_notification_message p').html( message );
		$('.wcwp_notification_popup').removeClass('is-success is-error');

		if ( 'error' === success_or_error ) {
			$( '.wcwp_notification_popup' ).addClass( 'active is-error' );
		} else if ( 'success' === success_or_error ) {
			$( '.wcwp_notification_popup' ).addClass( 'active is-success' );
		}

		// Dismiss the notification after 3 secs.
		setTimeout( function () {
			wcwp_hide_notification();
		}, 3000 );
	}

	/**
	 * Function to hide notification
	 */
	function wcwp_hide_notification() {
		$( '.wcwp_notification_popup' ).removeClass( 'active' );
	}

} );
