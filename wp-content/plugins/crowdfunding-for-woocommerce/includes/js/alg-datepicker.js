/**
 * alg-datepicker
 *
 * @version 3.0.0
 * @author  Algoritmika Ltd.
 */

jQuery( document ).ready( function() {
	jQuery( 'input[display="alg_crowdfunding_date"]' ).datepicker( {
		dateFormat: 'yy/mm/dd',
		firstDay: 1
	} );
	jQuery( 'input[display="alg_crowdfunding_time"]' ).timepicker();
} );
