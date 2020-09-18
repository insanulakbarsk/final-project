/**
 * alg-progressbar.
 *
 * @version 2.5.0
 * @since   2.3.0
 * @todo    [dev] `bar.text.style.fontFamily`
 * @todo    [dev] check JS coding standards
 */

jQuery(document).ready(function() {
	jQuery('.alg-progress-bar').each(function() {
		var the_type                        = jQuery(this).attr('type');
		var the_color                       = jQuery(this).attr('color');
		var text_color                      = jQuery(this).attr('text_color');
		var text_position                   = jQuery(this).attr('text_position');                   // line only
		var text_position_variable_max_left = jQuery(this).attr('text_position_variable_max_left'); // line only
		var text_top                        = jQuery(this).attr('text_top');                        // line only
		var the_value                       = jQuery(this).attr('value');                           // line only
		var text_right                      = 'inherit';                                            // line only
		var text_left                       = 'inherit';                                            // line only
		if ( 'variable' == text_position ) {
			text_left = Math.round(the_value * 100);
			if (text_left > text_position_variable_max_left) {
				text_left = text_position_variable_max_left;
			}
			text_left = text_left + '%';
		} else if ( 'left' == text_position ) {
			text_left = 0;
		} else { // if ( 'right' == text_position ) // default
			text_right = 0;
		}
		if ( 'line' == the_type ) {
			var bar = new ProgressBar.Line(this, {
				strokeWidth: 4,
				easing: 'easeInOut',
				duration: 1400,
				color: the_color,
				trailColor: '#eee',
				trailWidth: 1,
				svgStyle: {width: '100%', height: '100%'},
				text: {
					style: {
						color: text_color,
						position: 'absolute',
						right: text_right,
						left: text_left,
						top: text_top,
						padding: 0,
						margin: 0,
						transform: null
					},
					autoStyleContainer: false
				},
				from: {color: the_color},
				to: {color: '#ED6A5A'},
				step: (state, bar) => {
					bar.setText(Math.round(bar.value() * 100) + ' %');
				}
			});
			bar.animate(jQuery(this).attr('value'));
		} else if ( 'circle' == the_type ) {
			var bar = new ProgressBar.Circle(this, {
				color: text_color,
				// This has to be the same size as the maximum width to prevent clipping
				strokeWidth: 4,
				trailWidth: 1,
				easing: 'easeInOut',
				duration: 1400,
				text: { autoStyleContainer: false },
				from: { color: the_color, width: 4 },
				to: { color: '#333', width: 4 },
				// Set default step function for all animate calls
				step: function(state, circle) {
					circle.path.setAttribute('stroke', state.color);
					circle.path.setAttribute('stroke-width', state.width);
					var value = Math.round(circle.value() * 100);
					if (value === 0) {
						circle.setText('');
					} else {
						circle.setText(value + '%');
					}
				}
			});
			bar.text.style.fontFamily = '"Raleway", Helvetica, sans-serif';
			bar.text.style.fontSize = '2rem';
			bar.animate(jQuery(this).attr('value'));
		}
	});
});
