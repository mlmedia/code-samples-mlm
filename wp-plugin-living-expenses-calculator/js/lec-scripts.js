/**
 * re-vamped calculator JS to include wizard navigation?
 */
/* define $ as jQuery */
(function($) {

	/* building as a plugin to reuse for multiple instances */
	/* NOTE: currently the back end does not support multiple but could with some refactoring */
	$.fn.calculator = function() {
		/* set static vars */
		var calculator = this;
		var lead_form = $(calculator).find('.lec-lead-capture-form');
		var report = $(calculator).find('.lec-report');
		var currency_inputs = $(calculator).find('.currency-input');

		/* set global vars */
		/* TODO: set as options? */
		var speed = 300;

		function copy_to_report(source_id) {
			if (source_id) {
				var source_val = parseFloat($('#' + source_id).val().replace(/,/g,''));
				if (isNaN(source_val)) {
					var source_val = '0.00';
					$('#' + source_id).val(source_val);
				}
				var target = $(report).find('[data-source="' + source_id + '"]');
				$(target).text(format_dollars(source_val, true)).attr('data-value', source_val);
			};
		};

		function copy_total_to_report(source_id) {
			if (source_id) {
				var source_val = strip_dollars($('#' + source_id).text());
				var target = $(report).find('[data-source="' + source_id + '"]');
				$(target).text(format_dollars(source_val, true));
			};
		};

		/* calculate total per section / tab */
		function calculate_section_total(target, fill_blanks = false) {
			if (target) {
				var sum = 0;
				var section_items = $(target).find('.item--input input');
				var section_total_el = $(target).find('.item--section-total');
				var section_total_id = $(section_total_el).attr('id');
				$(section_items).each(function() {
					var value = parseFloat($(this).val().replace(/,/g,''));
					if (!isNaN(value)) {
						sum += value;
					} else {
						if (fill_blanks) {
							$(this).val('0.00');
						};
					};
				});
				$(section_total_el).text(format_dollars(sum)).attr('data-value', sum);
				copy_total_to_report(section_total_id);
			};
		};

		/* calculate total per section / tab */
		function calculate_grand_total() {
			var sum = 0;
			var section_totals = $(report).find('.report--section-your-total');
			var grand_total_el = $(report).find('.report--your-grand-total');
			$(section_totals).each(function() {
				var fval = strip_dollars($(this).text().replace(/,/g,''));
				var value = parseInt(fval);
				if (!isNaN(value)) {
					sum += value;
				};
			});
			$(grand_total_el).text(format_dollars(sum, true));
		};

		/* run setup functions on init */
		$(window).on('load', function() {
			/* setup functions here: */
		});

		function format_dollars(total, show_sign = false) {
			var output = '';
			if (total < 0) {
				total = Math.abs(total);
			}
			if (show_sign) {
				output += '$';
			}
			output += parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
			return output;
		};

		function strip_dollars(amount) {
			return amount.replace(/[^\d\.]/g, '');
		}

		/**
		 * scroll back up to top of form while navigating between tabs etc.
		 */
		function scroll_reset() {
			/* scroll to the top of the content */
			var scrollto = $(calculator).offset().top - 52;
			$('html, body').stop().animate({
				'scrollTop': scrollto
			}, speed);
		};

		/**
		 * set active navigation (inline navigation and tab nav)
		 */
		function set_active_nav(target) {
			if (target) {
				$('.tab-pane.active').hide(0, function() {
					$(this).removeClass('active');
					$('.tab-pane' + target).fadeIn(speed, function() {
						$(this).addClass('active');
					});
				});
				scroll_reset();
			};
		};

		/**
		 * event binding
		 */
		/* copy report after filling out input element */
		$(calculator).on('blur', 'input', function(e) {
 			var source_id = $(this).attr('id');
			var curr_step = $(this).parents('.tab-pane');
 			copy_to_report(source_id);
			calculate_section_total(curr_step);
 		});

		/* place cursor at the beginning to avoid getting stuck in the cents */
		$(currency_inputs).on('focus click', function() {
			/* clear out the value if 0.00 or empty */
			if (this.value < 0.01) {
				this.value = '';
			};
			$(this)[0].setSelectionRange(0, 0);
		});

		/* formatting to $123.45 */
		$(currency_inputs).on('change', function() {
			this.value = parseFloat(this.value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
		});

		/* expand the averages column */
		$(calculator).on('click', '.show-average-control', function(e) {
			e.preventDefault();
			var tab = $(this).parents('.tab-pane');
			var averages = $(tab).find('.item--average');
			$(averages).stop(
				true,
				true
			).animate({
				width: 'toggle',
				opacity: 'toggle'
			}, speed);
			if (!$(this).hasClass('active')) {
				$(this).addClass('active').text('Hide Area Averages');
			} else {
				$(this).removeClass('active').text('Show Area Averages');
			};
		});

		/* copy the average value to the input */
		$(calculator).on('click', '.average-copy-control', function(e) {
			e.preventDefault();
			var item = $(this).parents('.item');
			var target_input = $(item).find('.item--input input')
			var average_value = $(this).attr('data-value');
			$(target_input).val(average_value);

			/* copy directly to report (blur also copies to report) */
			var source_id = $(target_input).attr('id');
			var curr_step = $(this).parents('.tab-pane');
			copy_to_report(source_id);
			calculate_section_total(curr_step);
		});

		/* tab navigation */
		$(calculator).on('click', '.nav-item.clickable', function(e) {
			e.preventDefault();
			if (!$(this).hasClass('active')) {
				$('.nav-item').removeClass('active clickable step-complete');
				$(this).addClass('active').prevAll().addClass('step-complete clickable');
				var target = $(this).attr('href');
				set_active_nav(target);
			};
			calculate_grand_total();
		});

		/* wizard step navigation (bottom buttons) */
		$(calculator).on('click', '.tab-nav-item', function(e) {
			e.preventDefault();
			var curr_step = $(this).parents('.tab-pane');
			var nav_item = $(this).attr('data-nav');
			var nav_target = '#' + nav_item;
			/* calculate the total for the current section / tab */
			calculate_section_total(curr_step, true);
			calculate_grand_total();

			if (!$(nav_target).hasClass('active')) {
				$('.nav-item').removeClass('active clickable step-complete');
				$(nav_target).addClass('active').prevAll().addClass('step-complete clickable');
				var target = $(nav_target).attr('href');
				set_active_nav(target);
			};
		});

		/* on submit button click */
		$(lead_form).submit(function(e) {
			e.preventDefault();
			e.stopImmediatePropagation(); /* preventing the submission of the form twice - submit() is temperamental */

			/* clear the local (browser) storage */
			localStorage.clear();

			/* set vars */
			var form = this; /* the submitted element is the form */
			var action = $(form).attr('action');
			var email_val = $(form).find('input[name="email"]').val();
			var firstname_val = $(form).find('input[name="firstname"]').val();
			var lastname_val = $(form).find('input[name="lastname"]').val();

			/* submit w/ AJAX on click */
			var formdata = {
				"fields": [{
						"name": "email",
						"value": email_val
					},
					{
						"name": "firstname",
						"value": firstname_val
					},
					{
						"name": "lastname",
						"value": lastname_val
					}
				],
				/* TODO: check if we are using this: */
				// "legalConsentOptions": {
				// 	"consent": {
				// 		"consentToProcess": true,
				// 		"text": "I agree to allow Example Company to store and process my personal data.",
				// 		"communications": [{
				// 			"value": true,
				// 			"subscriptionTypeId": 999,
				// 			"text": "I agree to receive marketing communications from Example Company."
				// 		}]
				// 	}
				// }
			};
			var parsed_data = JSON.stringify(formdata);
			var settings = {
				"async": true,
				"crossDomain": true,
				"url": action,
				"method": "POST",
				"headers": {
					"Content-Type": "application/json",
					"Accept": "*/*",
					"Cache-Control": "no-cache", /* needs both? */
					"cache-control": "no-cache" /* needs both? */
				},
				"processData": false,
				"data": parsed_data
			}
			$.ajax(settings).done(function(response) {
				console.log(response);
				$('.lec-lead-capture').hide();
				$('.lec-report').fadeIn();
				scroll_reset();
			});
		});
	};

	/* doc ready */
	$(function() {

		/**
		 * init the calculator
		 * NOTE: we do it this way to have more than one instance on the same page
		 * (albeit unlikely)
		 */
		$('.lec-calculator').calculator();
	});
})(jQuery);
