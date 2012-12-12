(function() {
	jQuery(function($) {
		
		/*@ URL: quick_import/mapping */
		// checkboxes
		var $master = $('#mapping_all_checkbox');
		var $rows = $('.row_checkbox');
		
		// master checkbox for all rows
		$master.bind('click',function(e, preventDefault){
			if (preventDefault) {
				e.preventDefault();
			}
			if ( ! $master.is(':checked') ) {
				$rows.removeAttr('checked');
				$rows.parents('tr').addClass('unselected');
			}
			else {
				$rows.attr('checked', 'checked');
				$rows.parents('tr').removeClass('unselected');
			}
		}).trigger('click', true);
		// single checkbox disabling
		$rows.bind('click',function(e){
			var $row = $(this);
			
			if ( ! $row.is(':checked') ) {
				$row.parents('tr').addClass('unselected');
				$master.removeAttr('checked');
			}
			else {
				$row.parents('tr').removeClass('unselected');
			}
		});
		
		function get_selected_rows() {
			$rows.parents('tr');
		}
		
		function get_invalid_rows() {
			// rows which are included but no option is selected yet.
			return get_invalid_row_selects().parents('tr').find('.row_checkbox');
		}
		
		function get_invalid_row_selects() {
			// rows which are included but no option is selected yet.
			return $rows.parents('tr:not(.unselected)').find('select[name^=destination] option:lt(1):selected');
		}
		
		// form validation
		var $form = $('#mapping_form');
		$form.bind('submit', function(e) {
			if ( get_invalid_row_selects().length > 0 ) {
				e.preventDefault();
				//@ todo: better error handling
				alert('Some included rows have not been mapped yet.');
			}
		});
		
	});
})();