jQuery( document ).ready( function( $ ) {
	'use strict';

	/**
	 * Get the wholesale data values on quick edit.
	 */
	$( document ).on( 'click', '.editinline', function( e ) {
		e.preventDefault();

		var this_btn          = $( this );
		var post_id           = this_btn.closest( 'tr' ).attr( 'id' );
		post_id               = post_id.replace( 'post-', '' );
		var wholesale_min_qty = $( '#_wholesale_min_qty_' + post_id ).val();
		var wholesale_price   = $( '#_wholesale_price_' + post_id ).val();
		var product_type      = $( '#_wcwp_product_type_' + post_id ).val();

		if ( 'simple' === product_type ) {
			// Set the values now.
			$( 'input[name="_wholesale_min_qty"]', '.inline-edit-row' ).val( wholesale_min_qty );
			$( 'input[name="_wholesale_price"]', '.inline-edit-row' ).val( wholesale_price );
		} else {
			// Remove the quick edit wholesale pricing options.
			$( '#edit-' + post_id ).find( '.wcwp_wholesale_price_fields' ).remove();
			$( '#edit-' + post_id ).find( '.wcwp_wholesale_price_fields_title' ).remove();
		}
	} );
} );
