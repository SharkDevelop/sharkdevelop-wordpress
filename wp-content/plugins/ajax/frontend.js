if (typeof EZFC_Object === "undefined") {

EZFC_Object = function($) {
	var _this = this;

	var ezfc_hidden_value = "__HIDDEN__";
	// conditional parse exceptions
	var conditional_value_parse_exceptions_array = ["in", "not_in", "selected", "not_selected", "selected_index", "not_selected_index", "selected_id", "not_selected_id", "selected_count", "not_selected_count"];

	this.init = function() {
		// do not init twice
		if (typeof EZFC_LOADED !== "undefined") return;
		EZFC_LOADED = true;

		// form vars
		this.form_vars = [];
		// DOM form elements cached
		this.$form_elements = [];
		// element triggered change
		this.$element_trigger = 0;
		this.element_trigger_id = 0;
		// form trigger call list
		this.trigger_list = [];

		// form element subtotals array
		this.subtotals = [];
		// conditional once values
		this.conditional_once = [];

		// payment form id
		this.payment_form_id = 0;
		// payment methods
		this.payment_methods = ["stripe", "authorize"];

		// caching elements when using custom JS functions
		this.elements_cache = [];

		// group repeat elements
		this.group_repeat_elements = [];

		// uploaded files
		this.uploaded_files = [];

		// skip elements with these classes on startup blur (modifies value)
		this.skip_init_blur_classes = ["ezfc-slider", "ezfc-spinner"];
		// current steps
		this.current_steps = [];
		// step change flag
		this.step_is_changing = false;

		/**
			global functions for custom calculation codes
		**/
		this.functions = {
			calculate_element: function(form_id, element_id) {
				return _this.calculate_element(form_id, element_id);
			},
			calculate_price: function(form_id) {
				var $form = $(".ezfc-form[data-id='" + form_id + "']");
				return _this.calculate_price($form);
			},
			get_calculated_value_from: function(form_id, id) {
				return _this.get_calculated_element_value(form_id, id);
			},
			get_element_id_by_name: function(form_id, name) {
				if (!name && form_id) name = form_id; // shorthand

				var $element = $(".ezfc-form [data-elementname='" + name + "']");
				if (!$element) return false;

				if (!form_id) {
					form_id = $element.closest(".ezfc-form").data("id");
				}

				// no form element found
				if (!form_id) return false;

				for (var i in ezfc_vars.element_vars[form_id]) {
					var element = ezfc_vars.element_vars[form_id][i];
					var element_name = (_this.get_element_object_value(element, "name", "")).toLowerCase();
					name = name.toLowerCase();

					if (element_name == name) {
						return element.id;
					}
				}

				return false;
			},
			get_selected_id: function(element) {
				var id;

				// element is ID
				if (typeof element === "number") {
					id = element;
				}
				else if (typeof element === "string") {
					id = _this.functions.get_element_id_by_name(null, element);

					if (!id) {
						_this.debug_message("Unknown element: " + element);
						return false;
					}
				}
				else {
					_this.debug_message("Unknown object type: " + element);
					return false;
				}

				var selected_ids = _this.functions.get_value_from(id, true, true, { return_value: "option_id" });

				// return single ID
				if (selected_ids.length == 1) return selected_ids[0];

				return _this.functions.get_value_from(id, true, true, { return_value: "option_id" });
			},
			get_value_from: function(id, is_text, ignore_factor, options) {
				return _this.get_value_from_element(null, id, is_text, ignore_factor, options);
			},
			get_value_from_name: function(form_id, name, is_text) {
				var id = _this.functions.get_element_id_by_name(form_id, name);

				return _this.functions.get_value_from(id, is_text);
			},
			price_format: function(form_id, price, currency, custom_price_format, format_with_currency) {
				return _this.format_price(form_id, price, currency, custom_price_format, format_with_currency);
			}
		};
		// global functions for custom calculation codes / wrapper
		ezfc_functions = this.functions;

		// listeners for external values on change
		this.external_listeners = [];
		this.price_old_global = [];

		numeral.register("locale", "ezfc", {
			delimiters: {
				decimal:   ezfc_vars.price_format_dec_point,
				thousands: ezfc_vars.price_format_dec_thousand
			},
			abbreviations: {
				thousand: 'k',
				million: 'm',
				billion: 'b',
				trillion: 't'
			},
			ordinal: function (number) {
				var b = number % 10;
				return (~~ (number % 100 / 10) === 1) ? 'th' :
					(b === 1) ? 'st' :
					(b === 2) ? 'nd' :
					(b === 3) ? 'rd' : 'th';
			},
			currency: {
				symbol: '$'
			}
		});
		numeral.locale("ezfc");

		this.defaultFormat = ezfc_vars.price_format ? ezfc_vars.price_format : "0,0[.][000000]";
		numeral.defaultFormat(this.defaultFormat);

		// datepicker language
		$.datepicker.setDefaults($.datepicker.regional[ezfc_vars.datepicker_language]);

		// init form vars
		this.init_form_vars();

		// init js events
		this.attach_events();

		// init tooltips
		this.init_tips();
	};

	this.attach_events = function() {
		// image option listener
		$(document).on("click", ".ezfc-element-option-image", function() {
			// radio option image listener
			if ($(this).hasClass("ezfc-element-radio-image")) {
				_this.radio_change_state($(this).siblings(".ezfc-element-radio-input"));
			}
			else if ($(this).hasClass("ezfc-element-checkbox-image")) {
				_this.checkbox_change_state($(this).siblings(".ezfc-element-checkbox-input"));
			}
		});
		// image option label
		$(document).on("click", ".ezfc-element-option-has-image label", function() {
			var element_type  = $(this).closest(".ezfc-element").data("element");
			var $option_input = $(this).closest(".ezfc-element-single-option-container").find(":input");

			if (element_type == "radio") {
				_this.radio_change_state($option_input);
			}
			else if (element_type == "checkbox") {
				_this.checkbox_change_state($option_input, null, false, true);
			}
		});
		// addon div
		$(".ezfc-addon-option").click(function() {
			$(this).siblings(".ezfc-element-option-image").click();
		});

		// init each form
		$(".ezfc-form").each(function() {
			var $form    = $(this);
			var $wrapper = $form.closest(".ezfc-wrapper");

			_this.init_form_ui($form);

			if ($wrapper.find(".ezfc-form-loading-text").length) {
				$wrapper.find(".ezfc-form-loading-text").fadeOut(500, function() {
					$form.fadeIn(500, function() {
						$wrapper.removeClass("ezfc-form-loading");

						_this.form_change($form);
						_this.scroll();
					});
				});
			}

			// trigger custom event
			$(document).trigger("ezfc_forms_loaded");
		});

		// init fileupload
		$(".ezfc-element-fileupload").each(function(i, el) {
			var $parent  = $(this).closest(".ezfc-element");
			var $btn     = $parent.find(".ezfc-upload-button");
			var $list    = $parent.find(".ezfc-fileupload-files");
			var fe_id    = $parent.data("id");
			var multiple = $(this).attr("multiple") ? true : false;

			// build form data
			var $form    = $(this).closest(".ezfc-form");
			var form_id  = $form.find("input[name='id']").val();
			var ref_id   = $form.find("input[name='ref_id']").val();

			var formData = {
				action: "ezfc_frontend_fileupload",
				data: "action=upload_file&id=" + form_id + "&ref_id=" + ref_id
			};

			if (typeof $.prototype.fileupload !== "function") {
				_this.debug_message("Unable to load fileupload function.");
				return false;
			}

			$(this).fileupload({
				formData: formData,
				dataType: 'json',
				add: function (e, data) {
					$parent.find(".ezfc-bar").css("width", 0);
					$parent.find(".progress").addClass("active");
					$parent.find(".ezfc-fileupload-message").text("");

					$btn.off("click");

					// auto upload files
					if (_this.get_element_object_value(fe_id, "file_upload_auto", 1) == 1) {
						_this.file_upload_attach_event(el, data, $btn, e);
					}
					// add button event listener
					else {
						$btn.click(function() {
							_this.file_upload_attach_event(el, data, $btn, e);
						});
					}
					
				},
				done: function (e, data) {
					$btn.removeAttr("disabled");

					if (data.result.error) {
						$parent.find(".ezfc-fileupload-message").text(data.result.error);
						$parent.find(".ezfc-bar").css("width", 0);

						return false;
					}

					// check if list for form exists
					if (typeof _this.uploaded_files[form_id] === "undefined") {
						_this.uploaded_files[form_id] = [];
					}
					// check if list for form element exists
					if (typeof _this.uploaded_files[form_id][fe_id] === "undefined") {
						_this.uploaded_files[form_id][fe_id] = [];
					}

					for (var i in data.result) {
						// add uploaded file to list
						_this.uploaded_files[form_id][fe_id].push({
							id:   data.result[i].success,
							name: data.files[i].name
						});
					}

					// clear selected file
					$(this).val("");

					if (!$(this).attr("multiple")) {
						$(this).attr("disabled", "disabled");
					}

					// clear progressbar
					$parent.find(".progress").removeClass("active");
					// add message
					$parent.find(".ezfc-fileupload-message").text(ezfc_vars.upload_success);

					// build list
					var list_out = "";
					for (var i in _this.uploaded_files[form_id][fe_id]) {
						list_out += "<li class='ezfc-fileupload-files-item' data-fileid='" + _this.uploaded_files[form_id][fe_id][i].id + "' data-refid='" + ref_id + "'>" + _this.uploaded_files[form_id][fe_id][i].name + " <span class='ezfc-fileupload-files-remove'><i class='fa fa-close'></i></span></li>";
					}
					$list.html(list_out);
				},
				progressall: function (e, data) {
					var progress = parseInt(data.loaded / data.total * 100, 10);
					$($parent).find(".ezfc-bar").css("width", progress + "%");
				},
				replaceFileInput: false,
				url: ezfc_vars.ajaxurl
			});
		});
		// fileupload remove
		$(document).on("click", ".ezfc-fileupload-files-remove", function() {
			var $btn = $(this);
			if ($btn.hasClass("ezfc-state-removing")) return false;

			var $form    = $btn.closest(".ezfc-form");
			var form_id  = $form.find("input[name='id']").val();

			var $wrapper = $btn.closest(".ezfc-element");
			var $item    = $btn.closest(".ezfc-fileupload-files-item");
			var $msg     = $wrapper.find(".ezfc-fileupload-message");
			var $upload  = $wrapper.find(".ezfc-element-fileupload");

			var fe_id     = $wrapper.data("id");
			var file_id   = $item.data("fileid");
			var ref_id    = $item.data("refid");
			var send_data = "action=remove_uploaded_file&file_id=" + file_id + "&ref_id=" + ref_id;

			// add removing state
			$btn.addClass("ezfc-state-removing");
			// toggle icon
			$btn.find(".fa").removeClass("fa-close").addClass("fa-circle-o-notch fa-spin");

			$.ajax({
				type: "post",
				url: ezfc_vars.ajaxurl,
				data: {
					action: "ezfc_frontend_fileupload",
					data: send_data
				},
				complete: function(response) {
					var response_json;

					// check for errors
					if (typeof response === "object" && typeof response.responseText !== "undefined") {
						try {
							response_json = $.parseJSON(response.responseText);
						}
						catch(e) {}
					}

					if (!response_json) {
						$msg.text(response.upload_remove_error);
						
						// toggle icon
						$btn.find(".fa").removeClass("fa-circle-o-notch fa-spin ezfc-state-removing").addClass("fa-close");

						return false;
					}

					// re-enable multiple
					if (!$upload.attr("multiple")) {
						var files = $wrapper.find(".ezfc-fileupload-files-item").length;

						// item is still in the DOM-tree
						if (files == 1) {
							$upload.removeAttr("disabled");
						}
					}

					// remove item
					$item.fadeOut();
					$msg.text(ezfc_vars.upload_removed);

					// remove item from uploaded list
					for (var i in _this.uploaded_files[form_id][fe_id]) {
						if (_this.uploaded_files[form_id][fe_id][i].id == file_id) {
							_this.uploaded_files[form_id][fe_id].splice(i, 1);
						}
					}
				}
			});

			return false;
		});

		$(".ezfc-overview").dialog({
			autoOpen: false,
			modal: true
		});

		/**
			ui events
		**/
		$(".ezfc-form .ezfc-input-format-listener").each(function() {
			_this.input_format_listener_change($(this));

			$(this).trigger("ezfc_blur_trigger");
		});

		// form has changed -> recalc price
		var input_change_func_throttle = this.throttle(function($element, ev) {
			var form = $element.parents(".ezfc-form");
			_this.form_change(form, null, $element, ev);
		}, 100);

		$(document).on("change keyup", ".ezfc-form input, .ezfc-form select", function(ev) {
			input_change_func_throttle($(this), ev);
		});
		// textarea elements blur only
		$(document).on("blur", ".ezfc-form textarea", function(ev) {
			var form = $(this).closest(".ezfc-form");
			_this.form_change(form, null);
		});

		// checkbox
		$(".ezfc-element-wrapper-checkbox").change(function() {
			_this.checkbox_change($(this));
		});

		// radio
		$(document).on("click", ".ezfc-element-wrapper-radio :input", function() {
			_this.radio_change_state($(this), true);
		});

		// number-slider
		$(".ezfc-slider").each(function(i, el) {
			_this.init_slider($(el));
		});

		// number-spinner
		$(".ezfc-spinner").each(function(i, el) {
			_this.init_spinner($(el));
		});

		// steps
		$(document).on("click", ".ezfc-step-button", function() {
			var form_wrapper = $(this).parents(".ezfc-form");
			var current_step = parseInt(form_wrapper.find(".ezfc-step-active").data("step"));
			var next_step    = current_step + ($(this).hasClass("ezfc-step-next") ? 1 : -1);
			var verify_step  = $(this).hasClass("ezfc-step-next") ? 1 : 0;

			_this.set_step(form_wrapper, next_step, verify_step);
			return false;
		});
		// steps indicator
		$(document).on("click", ".ezfc-step-indicator-item-active, .ezfc-step-indicator-item-force", function() {
			var $form = $(this).closest(".ezfc-form");
			var step = parseInt($(this).data("step"));

			_this.set_step($form, step, 0);
			return false;
		});

		// payment option text switch
		$(document).on("change", ".ezfc-element-wrapper-payment input", function() {
			var $form   = $(this).closest(".ezfc-form");
			var form_id = $form.data("id");

			// submit text will be toggled by this.price_request_toggle()
			if (_this.form_vars[form_id].price_show_request == 1 || _this.form_vars[form_id].summary_enabled == 1) return;

			_this.set_submit_text($form);
		});

		// fixed price
		$(window).scroll(this.scroll);
		this.scroll();

		// submit button
		$(".ezfc-form").submit(function(e) {
			var $form   = $(this);
			var id      = $form.data("id");
			var $submit = $form.find(".ezfc-submit");

			if (_this.form_vars[id].hard_submit == 1) {
				return true;
			}

			_this.form_submit($form, -1, $submit.data("type"));

			e.preventDefault();
			return false;
		});
		// payment submit button
		$(".ezfc-payment-submit").click(function(e) {
			var $payment_form = $(this).closest(".ezfc-payment-form");
			var form_id       = $payment_form.data("form_id");
			var $form         = $(".ezfc-form[data-id='" + form_id + "']");

			_this.form_submit($form, -1, $(this).data("payment"));

			e.preventDefault();
			return false;
		});

		// payment cancel
		$(".ezfc-payment-cancel").click(function() {
			var $payment_form = $(this).closest(".ezfc-payment-form");
			var form_id       = $payment_form.data("form_id");
			var $form         = $("#ezfc-form-" + form_id);

			var selectors = ".ezfc-payment-dialog-modal[data-form_id='" + form_id + "']";
			selectors    += ", .ezfc-payment-form";

			$(selectors).removeClass("ezfc-payment-dialog-open");
			_this.form_submit($form, false, false, true);
			return false;
		});

		// reset button
		$(".ezfc-reset").click(function() {
			var $form = $(this).parents(".ezfc-form");
			_this.reset_form($form);

			return false;
		});

		// collapsible groups
		$(".ezfc-collapse-title-wrapper").on("click", function() {
			var $group_wrapper = $(this).closest(".ezfc-element-wrapper-group");

			_this.toggle_group($group_wrapper);
		});

		// credit card number formatter
		$(".ezfc-cc-number-formatter").on("change keyup", function() {
			var value = $(this).val();
			value = value.replace(/[^\dA-Z]/g, '').replace(/(.{4})/g, '$1 ').trim();
			$(this).val(value);
		});

		// table order buttons
		$(document).on("click", ".ezfc-table_order-btn", function(ev) {
			var $target = $("#" + $(this).data("target"));
			if ($target.is(":disabled")) return false;

			var $element     = $target.closest(".ezfc-custom-element");
			var form_id      = $element.closest(".ezfc-form").data("id");
			var min          = $target.data("min");
			var max          = $target.data("max");
			var value        = $(this).data("value");
			var value_target = _this.normalize_value($target.val());
			var value_new    = value_target + value;

			if (max == 0) max = value_new;

			value_new = Math.min(value_new, max);
			value_new = Math.max(value_new, min);

			$target.val(value_new);
			$target.trigger("blur");

			_this.form_change(null, form_id, null, ev);

			return false;
		});
		// table order input
		$(document).on("blur", ".ezfc-element-table_order-quantity-input", function(ev) {
			var $element = $(this).closest(".ezfc-custom-element");
			var form_id  = $element.closest(".ezfc-form").data("id");
			var min      = $(this).data("min");
			var max      = $(this).data("max");
			var value    = _this.normalize_value($(this).val());

			if (max == 0) max = value;

			value = Math.min(value, max);
			value = Math.max(value, min);

			$(this).val(value);

			_this.form_change(null, form_id, null, ev);
		});

		// add repeatable group
		$(document).on("click", ".ezfc-repeatable-form-repeat-button", function() {
			_this.group_repeat($(this).data("id"));

			return false;
		});
		// remove repeatable group
		$(document).on("click", ".ezfc-repeatable-form-remove-button", function() {
			_this.group_remove($(this).data("id"), $(this).data("row"));
			return false;
		});

		// popup button
		$(".ezfc-form-open-popup-button").click(function() {
			var form_id = $(this).data("target");
			_this.popup_form_open(form_id);			

			return false;
		});
		// close popup
		$(".ezfc-form-popup-close").click(function() {
			var form_id = $(this).data("target");
			_this.popup_form_close(form_id);

			return false;
		});
	};

	/**
		init form UI
	**/
	this.init_form_ui = function(form_dom, build_groups) {
		if (typeof build_groups === "undefined") build_groups = true;

		var $form    = $(form_dom);
		var form_id  = $form.data("id");
		
		// datepicker
		$form.find(".ezfc-element-datepicker").each(function() {
			var $element = $(this);

			var el_settings = {};
			if ($element.data("settings")) {
				el_settings = $element.data("settings");
			}

			// get available days
			var available_days = [0, 1, 2, 3, 4, 5, 6];
			if (el_settings.available_days.length) {
				available_days = el_settings.available_days.split(",");
			}
			// convert to int
			available_days = available_days.map(function(d) {
				return parseInt(d);
			});

			var blocked_days = [];
			if (el_settings.blocked_days.length) {
				blocked_days = el_settings.blocked_days.split(",");
			}

			var datepicker_format = $.trim(_this.form_vars[form_id].datepicker_format);
			if (datepicker_format == "") datepicker_format = "dd/mm/yy";

			var yearRange = $.trim(el_settings.yearRange);
			if (yearRange == "") yearRange = "c-10:c+10";

			$element.datepicker({
				changeMonth:    el_settings.changeMonth ? el_settings.changeMonth : false,
				changeYear:     el_settings.changeYear ? el_settings.changeYear : false,
				dateFormat:     datepicker_format,
				firstDay:       el_settings.firstDay ? el_settings.firstDay : false,
				minDate:        el_settings.minDate ? el_settings.minDate : "",
				maxDate:        el_settings.maxDate ? el_settings.maxDate : "",
				numberOfMonths: el_settings.numberOfMonths ? parseInt(el_settings.numberOfMonths) : 1,
				showAnim:       el_settings.showAnim ? el_settings.showAnim : "fadeIn",
				showWeek:       el_settings.showWeek=="1" ? el_settings.showWeek : false,
				yearRange:      yearRange,
				beforeShowDay: function(date) {
					return _this.check_datepicker_days(date, available_days, blocked_days);
				}
			});

			$element.on("change keyup", function(ev) {
				var date  = $(this).datepicker("getDate");
				var check = _this.check_datepicker_days(date, available_days, blocked_days);

				var valid = true;
				// check if valid date and date is not blocked
				if (typeof check === "object" && !check[0]) valid = false;

				// blocked day
				if (!valid) $(this).val("");

				_this.form_change($form, null, null, ev);
			});

			// set today's date
			_this.check_datepicker_predefined_values($element);
		});

		// daterange
		$form.find(".ezfc-element-daterange").each(function() {
			var $element = $(this);

			var el_settings = $element.closest(".ezfc-element-daterange-container").data("settings");

			// get available days
			var available_days = [0, 1, 2, 3, 4, 5, 6];
			if (el_settings.available_days.length) {
				available_days = el_settings.available_days.split(",");
			}
			// convert to int
			available_days = available_days.map(function(d) {
				return parseInt(d);
			});

			var blocked_days = [];
			if (el_settings.blocked_days.length) {
				blocked_days = el_settings.blocked_days.split(",");
			}

			var datepicker_format = $.trim(_this.form_vars[form_id].datepicker_format);
			if (datepicker_format == "") datepicker_format = "dd/mm/yy";

			var yearRange = $.trim(el_settings.yearRange);
			if (yearRange == "") yearRange = "c-10:c+10";

			// from
			if ($element.hasClass("ezfc-element-daterange-from")) {
				$element.datepicker({
					changeMonth:    el_settings.changeMonth ? el_settings.changeMonth : false,
					changeYear:     el_settings.changeYear ? el_settings.changeYear : false,
					dateFormat:     datepicker_format,
					firstDay:       el_settings.firstDay ? el_settings.firstDay : false,
					minDate:        $element.data("mindate"),
					maxDate:        $element.data("maxdate"),
					numberOfMonths: el_settings.numberOfMonths ? parseInt(el_settings.numberOfMonths) : 1,
					showAnim:       el_settings.showAnim ? el_settings.showAnim : "fadeIn",
					showWeek:       el_settings.showWeek=="1" ? el_settings.showWeek : false,
					yearRange:      yearRange,
					onSelect: function(selectedDate) {
						var minDays = $element.data("mindays") || 0;
						var maxDays = $element.data("maxdays") || 0;

						var minDate = $.datepicker.parseDate(datepicker_format, selectedDate);
						minDate.setDate(minDate.getDate() + minDays);
						var maxDate = $.datepicker.parseDate(datepicker_format, selectedDate);
						maxDate.setDate(maxDate.getDate() + maxDays);

						$element.siblings(".ezfc-element-daterange-to").datepicker("option", "minDate", minDate);
						if (maxDays != 0) $element.siblings(".ezfc-element-daterange-to").datepicker("option", "maxDate", maxDate);
						$element.trigger("change");
					},
					beforeShowDay: function(date) {
						return _this.check_datepicker_days(date, available_days, blocked_days);
					}
				});

				// set today's date
				_this.check_datepicker_predefined_values($element);
			}
			// to
			else {
				$element.datepicker({
					changeMonth:    el_settings.changeMonth ? el_settings.changeMonth : false,
					changeYear:     el_settings.changeYear ? el_settings.changeYear : false,
					dateFormat:     datepicker_format,
					firstDay:       el_settings.firstDay ? el_settings.firstDay : false,
					minDate:        $element.data("mindate"),
					maxDate:        $element.data("maxdate"),
					numberOfMonths: el_settings.numberOfMonths ? parseInt(el_settings.numberOfMonths) : 1,
					showAnim:       el_settings.showAnim ? el_settings.showAnim : "fadeIn",
					showWeek:       el_settings.showWeek=="1" ? el_settings.showWeek : false,
					yearRange:      yearRange,
					onSelect: function(selectedDate) {
						$element.trigger("change");
					},
					beforeShowDay: function(date) {
						return _this.check_datepicker_days(date, available_days, blocked_days);
					}
				});

				// set today's date
				_this.check_datepicker_predefined_values($element);
			}
		});

		// timepicker
		$form.find(".ezfc-element-timepicker").each(function() {
			_this.init_timepicker($(this), form_id);
		});

		// colorpicker
		$form.find(".ezfc-element-colorpicker").each(function() {
			var $element = $(this);
			var input    = $element.parents(".ezfc-element").find(".ezfc-element-colorpicker-input");

			var colorpicker = $element.colorpicker({
				container: $element
			}).on("changeColor.colorpicker", function(ev) {
				$element.css("background-color", ev.color.toHex());
				input.val(ev.color.toHex());
			});

			$(input).on("click focus", function() {
				colorpicker.colorpicker("show");
			}).on("change", function() {
				colorpicker.colorpicker("setValue", $form.val());
			});
		});

		// if steps are used, move the submission button + summary table to the last step
		var steps = $form.find(".ezfc-step");
		if (steps.length > 0) {
			var last_step = steps.last();
			
			$form.find(".ezfc-summary-wrapper").appendTo(last_step);
			$form.find(".ezfc-submit-wrapper").appendTo(last_step).addClass("ezfc-submit-step");

			// prevent enter step in last step
			this.prevent_enter_step_listener(last_step.find("input"), $form);
		}

		_this.init_form(form_dom);
	};

	this.init_slider = function($element) {
		var element_id         = $element.data("id");
		var element            = this.get_element_object(element_id);
		var form_id            = $element.closest(".ezfc-form").data("id");
		var $slider_element    = $element.siblings(".ezfc-slider-element");
		var slider_orientation = $element.hasClass("ezfc-slider-vertical") ? "vertical" : "horizontal";
		var value_normalized   = this.normalize_value($element.val(), $element);

		var slider_options = {
			min:         $element.data("min") || 0,
			max:         $element.data("max") || 100,
			step:        $element.data("stepsslider") || 1,
			value:       value_normalized || 0,
			orientation: slider_orientation
		};

		// fixed slider values
		var slider_values_array = $element.data("values");
		var slider_values       = null;
		if (slider_values_array) {
			slider_values = String(slider_values_array).split(",");
			slider_values = $.map(slider_values, $.trim);

			slider_options.min    = 0;
			slider_options.max    = slider_values.length - 1;

			// find slider value
			for (var sv in slider_values) {
				if (slider_values[sv] == value_normalized) {
					slider_options.value = sv;
				}
			}
		}

		// slider functions
		slider_options.change = function(ev, ui) {
			var ui_value = _this.normalize_value($element.val(), $element);
			var value    = ui_value;

			// use fixed slider value
			if (slider_values) {
				for (var i in slider_values) {
					var normalized_ui_value = _this.normalize_value(ui_value, $element);

					if (ui_value == slider_values[i]) value = i;
				}
			}

			this.value = value;
		};
		slider_options.slide = function(ev, ui) {
			var ui_value = ui.value;

			// use fixed slider value
			if (slider_values) {
				if (typeof slider_values[ui.value] !== "undefined") {
					ui_value = slider_values[ui.value];
				}
			}

			// change value before trigger
			var value = _this.format_price(form_id, ui_value, false, false, false, true);
			
			$element.val(value);
			$element.trigger("change");
		};
		slider_options.start = function(ev, ui) {
			$element.trigger("focus");
		};
		slider_options.stop = function(ev, ui) {
			var ui_value = ui.value;

			// use fixed slider value
			if (slider_values && typeof slider_values[ui.value] !== "undefined") {
				ui_value = slider_values[ui.value];
			}

			var value = _this.format_value_element(element_id, ui_value);

			$element.val(value);
			$element.trigger("change");
			$element.trigger("blur");
		};

		// init slider
		var slider_object = $slider_element.slider(slider_options);

		if ($element.hasClass("ezfc-pips")) {
			var pips_args = { rest: "label" };

			if (slider_values) pips_args.labels = slider_values;

			// check if steps is a valid number
			if (!isNaN($element.data("stepspips")) && $element.data("stepspips") != "") pips_args.step = $element.data("stepspips");

			$slider_element.slider("pips", pips_args);
			$slider_element.find(".ui-slider-pip").on("click", function() {
				var pip_val = $(this).find(".ui-slider-label").data("value");

				if (slider_values && typeof slider_values[pip_val] !== "undefined") pip_val = slider_values[pip_val];
				pip_val = _this.format_price(form_id, pip_val, false, false, false, true);
				
				$element.val(pip_val);
				$element.trigger("change");
				$element.trigger("blur");
			});

			// check for floating pips
			if ($element.data("pipsfloat") == 1) $slider_element.slider("float");
		}
	};

	this.init_spinner = function($element) {
		var $parent_element = $element.closest(".ezfc-custom-element");
		var element_id = $parent_element.data("id");
		var form_id    = $element.closest(".ezfc-form").data("id");
		var steps      = $element.data("stepsspinner") || 1;

		$element.spinner({
			min:  $element.data("min") || 0,
			max:  $element.data("max") || 100,
			step: steps,
			change: function(ev, ui) {
			},
			spin: function(ev, ui) {
				// normalize
				var value = _this.format_price(form_id, ui.value, false, false, false, false);
			},
			start: function(ev, ui) {
				var ui_value = $element.val();
				var value = _this.normalize_value(ui_value);
				$element.val(value);
			},
			stop: function(ev, ui) {
				// normalize
				var ui_value = $element.val();
				ui_value = _this.normalize_value(ui_value, $element);
				var value = _this.format_value_element(element_id, ui_value);
				$element.val(value);
				$element.trigger("change");
				$element.trigger("blur");
			}
		});
	};

	this.init_timepicker = function($timepicker, form_id, add_settings) {
		var el_settings = {};
		if ($timepicker.data("settings")) {
			el_settings = $timepicker.data("settings");
		}

		if (typeof add_settings === "object") {
			el_settings = $.extend({}, el_settings, add_settings);
		}

		var timepicker_format = el_settings.format ? el_settings.format : _this.form_vars[form_id].timepicker_format;
		if (timepicker_format == "") timepicker_format = "H:i";

		$timepicker.timepicker({
			disableTimeRanges: el_settings.disabledTimes ? el_settings.disabledTimes : [],
			minTime:           el_settings.minTime ? el_settings.minTime : null,
			maxTime:           el_settings.maxTime ? el_settings.maxTime : null,
			step:              el_settings.steps ? el_settings.steps : 30,
			timeFormat:        timepicker_format
		});
	};

	/**
		init form vars
	**/
	this.init_form_vars = function() {
		for (var form_id in ezfc_vars.form_vars) {
			this.form_vars[form_id] = ezfc_vars.form_vars[form_id];

			// subtotals array
			this.subtotals[form_id] = [];
			// conditional once values
			this.conditional_once[form_id] = [];

			// init listener for each form
			this.external_listeners[form_id] = [];

			// elements cache
			this.elements_cache[form_id] = [];

			// step
			this.current_steps[form_id] = 0;

			// form trigger call list
			this.trigger_list[form_id] = [];

			// cache form elements
			$.each(this.form_vars[form_id].form_elements_order, function(i, element_id) {
				_this.form_vars[form_id].form_elements_order[i] = String(_this.form_vars[form_id].form_elements_order[i]);
				_this.add_form_element_to_cache(element_id, form_id);
			});
		}
	};

	this.add_form_element_to_cache = function(element_id, form_id) {
		if (typeof ezfc_vars.element_vars[form_id][element_id] === "undefined") return;

		var $element = $("#ezfc_element-" + element_id);

		var element_obj = {
			element: ezfc_vars.element_vars[form_id][element_id],
			$element: $element,
			$element_input: $element.find("input, textarea"),
			id: element_id,
			form_id: form_id
		};

		this.$form_elements[element_id] = element_obj;
	};

	/**
		init form
	**/
	this.init_form = function(form_dom) {
		var $form    = $(form_dom);
		var form_id  = $form.data("id");

		// ref id
		this.set_ref_id(form_id);

		// set request price text
		if (this.form_vars[form_id].price_show_request == 1) {
			this.price_request_toggle(form_id, false);
		}

		// set price
		this.price_old_global[form_id] = 0;
		this.set_price($form);
		// set submit text
		this.set_submit_text($form);

		// hide woocommerce price+button
		if (this.form_vars[form_id].use_woocommerce) {
			var elements_to_hide = ".woocommerce form.cart, .woocommerce .price";

			$(elements_to_hide).hide();
		}

		// step indicator start added by 1 so it becomes more readable
		var step_indicator_start = parseInt(this.form_vars[form_id].step_indicator_start) + 1;
		if (step_indicator_start > 1) {
			$form.find(".ezfc-step-indicator").hide();
		}

		// init debug info
		if (ezfc_vars.debug_mode == 2 && !$("#ezfc-show-all-elements").length) {
			$form.append("<button id='ezfc-show-all-elements'>Show/hide elements</button>");

			$("#ezfc-show-all-elements").click(function() {
				if ($form.hasClass("ezfc-debug-visible")) {
					$form.removeClass("ezfc-debug-visible");
					$form.find(".ezfc-tmp-visible").removeClass("ezfc-tmp-visible").hide();
				}
				else {
					$form.addClass("ezfc-debug-visible");
					$form.find(".ezfc-hidden").addClass("ezfc-tmp-visible").show().css("display", "inline-block");
				}

				return false;
			});
		}

		// stripe
		if (this.form_vars[form_id].use_stripe == 1 && typeof Stripe !== "undefined") {
			Stripe.setPublishableKey(ezfc_vars.stripe.publishable_key);
		}

		// prevent enter key to submit form
		this.prevent_enter_step_listener(":input:not(textarea):not([type=submit])", $form);

		// populate html placeholders
		this.populate_html_placeholders($form);

		if (this.form_vars[form_id].popup_open_auto == 1) {
			this.popup_form_open(form_id);
		}

		// init table order values
		this.init_table_order(form_id);

		for (var i in ezfc_vars.element_vars[form_id]) {
			var element = ezfc_vars.element_vars[form_id][i];

			if (element.type == "repeatable_form") {
				this.init_repeatable_group(element.id);

				if (element.repeatable == 1) {
					var clone_count = parseInt(this.get_element_object_value(element, "repeatable_count_start", 0));
					clone_count = Math.min(clone_count, this.get_element_object_value(element, "repeatable_count_max"));
					for (var n = 0; n < clone_count; n++) {
						this.group_repeat(element.id);
					}
				}
			}
		}

		// trigger custom event
		$(document).trigger("ezfc_form_init", form_id);
	};

	// form has changed
	this.form_change = function(form, form_id, $element_trigger, ev) {
		form    = form || $("#ezfc-form-" + form_id);
		form_id = form_id || $(form).data("id");

		// check for parent form
		if (!form || !form.length) {
			form = $element_trigger.closest(".ezfc-form");
			
			// still unable to find any form
			if (!form.length) return;
		}

		// reset form trigger call list
		this.trigger_list[form_id] = [];

		// set element trigger change
		this.$element_trigger = $element_trigger;

		// get element trigger ID
		this.element_trigger_id = 0;
		if ($element_trigger) {
			this.element_trigger_id = $element_trigger.data("id");

			if (!$element_trigger.hasClass("ezfc-custom-element")) {
				this.element_trigger_id = $element_trigger.closest(".ezfc-custom-element").data("id");
			}

			// only check if element is not of repeated type
			if (!this.element_trigger_id || !this.element_trigger_id.indexOf || this.element_trigger_id.indexOf("-") < 0) {
				// check if element has any trigger
				var element = this.get_element_object(this.element_trigger_id);
				
				// no trigger
				if (!element.hasOwnProperty("trigger_ids") || element.trigger_ids.length < 1) return;

				// build trigger list
				this.form_build_trigger_list(form_id, element.id);
			}
		}

		// clear hidden values
		this.clear_hidden_values(form);

		// form has changed -> reset price + summary
		this.form_vars[form_id].price_requested = 0;
		this.form_vars[form_id].summary_shown = 0;

		$(form).find(".ezfc-summary-wrapper").fadeOut();
		
		// price request
		if (this.form_vars[form_id].price_show_request == 1) {
			this.price_request_toggle(form_id, false);
		}
		
		// remove debug info
		this.remove_debug_info();

		this.set_price(form);
		this.set_submit_text(form);

		// populate html placeholder data
		this.populate_html_placeholders(form);

		// create live summary table
		this.live_summary_update(form_id);
		// update scroll position if necessary
		this.scroll();

		this.call_hook("ezfc_after_form_change", {
			form_id: form_id
		});
	};

	/**
		build element trigger list
	**/
	this.form_build_trigger_list = function(form_id, element_id) {
		var element = this.get_element_object(element_id);
		var trigger_list = [element_id];

		$.each(element.trigger_ids, function(i, trigger_element_id) {
			var trigger_element = _this.get_element_object(trigger_element_id);
			trigger_list = trigger_list.concat(trigger_element.trigger_ids);
		});

		this.trigger_list[form_id] = this.unique(trigger_list);
	};

	/**
		form submitted
	**/
	this.form_submit = function(form, step, submit_type, cancel) {
		var $form         = $(form);
		var $form_wrapper = $form.closest(".ezfc-wrapper");
		var id            = $form.data("id");

		this.call_hook("ezfc_form_submit_start", {
			data: data,
			form: $form,
			form_vars: this.form_vars[id],
			id: id
		});

		// if this flag is true, the ajax request will be cancelled
		var form_error = false;

		// show submit icon
		$form.find(".ezfc-submit-icon").addClass("ezfc-submit-icon-show");

		// set spinner icon to submit field
		var submit_icon    = $form.find(".ezfc-submit-icon, .ezfc-step-submit-icon");
		var submit_element = $form.find("input[type='submit']");

		// price request
		if (this.form_vars[id].price_show_request == 1) {
			this.set_price(form);
		}

		// cancel submit
		if (cancel) {
			_this.submit_cancel($form);
			return false;
		}

		// payment dialog
		if ($.inArray(submit_type, _this.payment_methods) !== -1) {
			var $payment_form = $("#ezfc-" + submit_type + "-form-" + id);

			// fill placeholders with total price
			var price_html = this.format_price(id, this.price_old_global[id], false, false, true);
			$payment_form.find(".ezfc-payment-price").text(price_html);

			// show cc details dialog
			if (!_this.form_vars[id].payment_info_shown[submit_type]) {
				_this.form_vars[id].payment_info_shown[submit_type] = 1;
				// open dialog + modal
				$("#ezfc-" + submit_type + "-form-" + id + ", #ezfc-" + submit_type + "-form-modal-" + id).addClass("ezfc-payment-dialog-open");
			}
			// create request token
			else {
				_this.payment_form_id = id;

				// disable action buttons
				$payment_form.find(".ezfc-payment-submit, .ezfc-payment-cancel").prop("disabled", true);

				if (submit_type == "stripe") {
					// create token
					Stripe.card.createToken($payment_form, this.stripe_response_handler);
				}
				else if (submit_type == "authorize") {
					if (typeof Accept === "undefined") {
						_this.debug_message("AcceptJS wasn't loaded.");
						return false;
					}

					this.authorize_create_token(id);
				}
			}

			submit_icon.fadeOut();
			return false;
		}

		// show loading icon, disable submit button
		submit_icon.fadeIn();
		submit_element.prop("disabled", true);

		// clear hidden elements (due to conditional logic)
		$form.find(".ezfc-custom-hidden:not(.ezfc-element-wrapper-fileupload):not(.ezfc-element-wrapper-group)").each(function() {
			// empty radio buttons --> select first element to submit __hidden__ data
			var radio_empty = $(this).find(".ezfc-element-radio:not(:has(:radio:checked))");
			if (radio_empty.length) {
				$(radio_empty).first().find("input").prop("checked", true);
			}

			// set hidden dropdown value
			$(this).find("option").first().prop("checked", true).val(ezfc_hidden_value);

			// set hidden checkbox value
			$(this).find(".ezfc-element-checkbox-input").first().prop("checked", true).addClass("ezfc-has-hidden-placeholder").val(ezfc_hidden_value);
			
			// todo: re-add disabled attribute after price request / summary
			$(this).find(":input").val(ezfc_hidden_value).removeAttr("disabled").addClass("ezfc-has-hidden-placeholder");
		});

		// add custom element data to request
		var elements_custom_data = "";

		for (var i in this.form_vars[id].form_elements_order) {
			var element_id = this.form_vars[id].form_elements_order[i];
			var element    = this.get_element_object(element_id);

			// skip repeatable form element
			if (element && element.type == "repeatable_form") continue;

			// repeatable element
			if (this.is_repeatable_element(element_id)) {
				var parent_id = this.get_repeatable_parent_id(element_id);
				if (!parent_id) continue;

				var real_element_id = this.get_repeatable_element_id(element_id);
				var counter_id = this.get_repeatable_element_counter(element_id);

				elements_custom_data += "&calculated_values[" + parent_id + "][" + real_element_id + "][" + counter_id + "]=" + this.get_calculated_element_value(id, element_id);
			}
			// default element
			else {
				var calculate_enabled = this.get_element_object_value(element_id, "calculate_enabled", 1);
				var element_is_visible = _this.element_is_visible(element_id);

				// add to visible array
				if (element_is_visible) {
					elements_custom_data += "&elements_visible[" + element_id + "]=1";
				}
				
				if (calculate_enabled == 0 && !element_is_visible) {
					var $element_input = this.get_element_child_input_dom(element_id);
					$element_input.val(ezfc_hidden_value);
				}

				// add calculated value
				var element_calculated_value = this.get_calculated_element_value(id, element_id);
				elements_custom_data += "&calculated_values[" + element_id + "]=" + element_calculated_value;
			}
		}

		// check for min/max selectable
		$form.find(".ezfc-element-wrapper-checkbox").each(function() {
			// check for hidden placeholder
			if ($(this).find(".ezfc-has-hidden-placeholder").length) return false;

			var $element     = $(this);
			var min_selected = parseInt($element.attr("data-min_selectable"));
			var max_selected = parseInt($element.attr("data-max_selectable"));
			var selected     = $element.find(":checked").length;

			// check min selected
			if (min_selected > 0 && selected < min_selected) {
				form_error = true;
				_this.show_tip($element, "#" + $element.attr("id"), 0, _this.sprintf(_this.form_vars[id].selectable_min_error, min_selected));

				_this.scroll_to($element);
			}
			// check max selected
			if (max_selected > 0 && selected > max_selected) {
				form_error = true;
				_this.show_tip($element, "#" + $element.attr("id"), 0, _this.sprintf(_this.form_vars[id].selectable_max_error, max_selected));

				_this.scroll_to($element);
			}
		});

		// store values in tmp array
		var values_before_submission = [];

		// serialize form data
		var data = $form.serialize();

		// add custom data
		data += elements_custom_data;

		// restore values
		$.each(values_before_submission, function(i, obj) {
			$(obj.element).val(obj.value);
		});

		if (form_error) {
			submit_icon.fadeOut();
			submit_element.removeAttr("disabled");
			_this.clear_hidden_values($form);
			return false;
		}

		// dynamic min/max
		$(".ezfc-element[data-min], .ezfc-element[data-max]").each(function() {
			var mm_element_id = $(this).closest(".ezfc-custom-element").data("id");
			var mm_min = $(this).data("min");
			var mm_max = $(this).data("max");

			var mm_data = "&dynamic_min[" + mm_element_id + "]=" + mm_min;
			mm_data    += "&dynamic_max[" + mm_element_id + "]=" + mm_max;

			data += mm_data;
		});

		// url
		data += "&url=" + encodeURI(window.location.href);

		// request price for the first time
		if (this.form_vars[id].price_requested == 0) {
			data += "&price_requested=1";
		}

		// summary
		if (this.form_vars[id].summary_shown == 0) {
			data += "&summary=1";
		}

		// next/previous step
		if (step != -1) {
			data += "&step=" + step;
		}

		// preview
		if (this.form_vars[id].preview_form) {
			data += "&preview_form=" + this.form_vars[id].preview_form;
		}

		// append generated price
		var generated_price = this.price_old_global[id];
		if (this.form_vars[id].price_show_request == 1) {
			generated_price = _this.functions.calculate_price(id);
		}
		data += "&generated_price=" + generated_price;

		this.call_hook("ezfc_before_submission", {
			data: data,
			form: $form,
			form_vars: this.form_vars[id],
			id: id
		});

		var req = $.ajax({
			type: "post",
			url: ezfc_vars.ajaxurl,
			data: {
				action: "ezfc_frontend",
				data: data
			},
			success: function(response) {
				$(".ezfc-submit-icon").removeClass("ezfc-submit-icon-show");

				submit_element.removeAttr("disabled");
				submit_icon.fadeOut();

				_this.debug_message(response);

				try {
					response = $.parseJSON(response);
				}
				catch (e) {
					response = false;
					_this.debug_message(e);
				}

				if (!response) {
					$("#ezfc-message-" + id).text(ezfc_vars.form_submit_error);

					_this.recaptcha_reload();
					_this.reset_disabled_fields(form, true);
						
					return false;
				}

				// error occurred -> invalid form fields
				if (typeof response.error !== "undefined") {
					_this.reset_disabled_fields(form, true);

					if (response.id) {
						// error tip (if the form uses steps, do not show this if all fields are valid up until this step)
						var show_error_tip = true;
						// error tip data
						var el_target = "#ezfc_element-" + response.id;
						var el_tip    = $(el_target).find(".ezfc-element").first();
						var tip       = null;

						// check if form uses steps
						var use_steps = $form.find(".ezfc-step-active").length > 0 ? true : false;
						if (use_steps) {
							var error_step = parseInt($(el_target).parents(".ezfc-step").data("step"));
							
							// if invalid field is not on the current step, do not show the tip. also, do not show the tip when submitting the form (step = -1)
							if (error_step != step && step != -1) {
								show_error_tip = false;
								_this.set_step(form, step + 1);
							}
						}

						if (show_error_tip) {
							if (!el_tip.length) {
								el_tip = $(el_target);
							}

							var tip_delay = use_steps ? 1000 : 400;
							var payment_dialog_open = $form_wrapper.find(".ezfc-payment-dialog-open").length > 0;

							// element is visible
							if (el_tip.is(":visible")) {
								_this.show_tip(el_tip, el_target, tip_delay, response.error);

								// payment dialog is opened
								if (payment_dialog_open) {
									_this.set_message(id, response.error + " (#" + response.id + ")");
								}
							}
							else {
								var $el_tip_parent_groups = el_tip.parents(".ezfc-element-wrapper-group");

								// element is hidden in a group
								if ($el_tip_parent_groups.length) {
									$el_tip_parent_groups.each(function(gi, group_el) {
										_this.toggle_group($(group_el), true);
									});

									_this.show_tip(el_tip, el_target, tip_delay, response.error);
								}
								// element is still hidden
								else {
									_this.set_message(id, response.error + " (#" + response.id + ")");
								}
							}

							// auto hide tooltip
							if (typeof ezfc_vars.required_text_auto_hide !== "undefined") {
								var required_text_auto_hide = parseFloat(ezfc_vars.required_text_auto_hide) * 1000;

								if (required_text_auto_hide > 0) {
									setTimeout(function() {
										if (tip) tip.hide();
									}, required_text_auto_hide);
								}
							}

							if (_this.form_vars[id].disable_error_scroll == 0) {
								_this.scroll_to(el_target);
							}
						}
					}
					else {
						_this.set_message(id, response.error);
					}

					_this.recaptcha_reload();

					return false;
				}
				// next step
				else if (response.step_valid) {
					_this.reset_disabled_fields(form);
					_this.set_step(form, step + 1);

					return false;
				}
				// summary
				else if (response.summary) {
					$form.find(".ezfc-summary-wrapper").fadeIn().find(".ezfc-summary").html(response.summary);
					_this.form_vars[id].summary_shown = 1;

					_this.reset_disabled_fields(form);

					return false;
				}

				// prevent spam
				_this.recaptcha_reload();

				// submit paypal form
				if (response.paypal) {
					// disable submit button again to prevent doubleclicking
					submit_element.attr("disabled", "disabled");
					// redirect to paypal express checkout url
					window.location.href = response.paypal;
				}
				else {
					// price request
					if (response.price_requested || response.price_requested === 0) {
						_this.price_request_toggle(id, true, response.price_requested);
						return false;
					}

					/**
						submission successful
					**/
					var hook_vars = {
						data: data,
						form: $form,
						form_vars: _this.form_vars[id],
						id: id,
						price: price,
						response: response
					};
					// hook
					_this.call_hook("ezfc_submission_success", hook_vars);
					// call custom js function
					if (_this.form_vars[id].submission_js_func && typeof window[_this.form_vars[id].submission_js_func] === "function") {
						window[_this.form_vars[id].submission_js_func](hook_vars);
					}

					// hide payment dialog(s)
					$form_wrapper.find(".ezfc-payment-dialog, .ezfc-payment-dialog-modal").removeClass("ezfc-payment-dialog-open");

					// add success text
					var $success_text = $(".ezfc-success-text[data-id='" + id + "']");
					$success_text.html(response.success);

					_this.set_ref_id(id);

					// reset form after submission
					if (_this.form_vars[id].reset_after_submission == 1) {
						_this.reset_form(form);

						// show success text
						var success_text_delay = 10000;
						if (_this.object_has_property(_this.form_vars[id], "show_success_text_duration")) {
							success_text_delay = parseFloat(_this.form_vars[id].show_success_text_duration) * 1000;
						}
						if (success_text_delay == 0) success_text_delay = 99999999999;

						$success_text.fadeIn().delay(success_text_delay).fadeOut();
						return;
					}

					// hide all forms
					if (_this.form_vars[id].hide_all_forms == 1) {
						$(".ezfc-form, .ezfc-required-notification").fadeOut().addClass("ezfc-form-submitted");
					}
					else {
						$form.find(".ezfc-required-notification").fadeOut();

						if (_this.form_vars[id].show_success_text == 1) {
							$form.fadeOut().addClass("ezfc-form-submitted");
						}
					}

					// show success text
					if (_this.form_vars[id].show_success_text == 1) {
						// scroll to success message
						if (_this.form_vars[id].scroll_to_success_message == 1) {
							$success_text.fadeIn(400, function() {
								_this.scroll_to($success_text);
							});
						}
						else {
							$success_text.fadeIn();
						}
					}

					// update mini cart
					if (response.woo_update_cart) {
						$(document.body).trigger("wc_fragment_refresh");

						_this.call_hook("ezfc_wc_submission_success", hook_vars);
					}

					var redirect_timer = Math.max(0, Math.abs(parseInt(_this.form_vars[id].redirect_timer)));

					// redirect the user
					if (typeof _this.form_vars[id].redirect_url !== "undefined" && _this.form_vars[id].redirect_url.length > 0) {
						var redirect_form_vars = "";

						if (_this.form_vars[id].redirect_forward_values == 1) {
							redirect_form_vars = $form.serialize();
							redirect_form_vars += "&total=" + _this.price_old_global[id];
							redirect_form_vars += "&total_f=" + _this.functions.price_format(id, _this.price_old_global[id]);
						}

						// insert key
						if (response.hasOwnProperty("insert_key")) redirect_form_vars += "&ezfc_insert_key=" + response.insert_key;

						var href_separator = _this.form_vars[id].redirect_url.indexOf("?") == -1 ? "?" : "&";

						setTimeout(function() {
							window.location.href = _this.form_vars[id].redirect_url + href_separator + redirect_form_vars;
						}, redirect_timer * 1000);
					}
					// refresh the page
					else if (typeof _this.form_vars[id].refresh_page_after_submission !== "undefined" && _this.form_vars[id].refresh_page_after_submission == 1) {

						setTimeout(function() {
							window.location.reload();
						}, redirect_timer * 1000);
					}
				}
			}
		});

		req.fail(function(jqXHR, testStatus, errorThrown) {
			_this.debug_message({ jqXHR: jqXHR, testStatus: testStatus, errorThrown: errorThrown });

			_this.set_message(id, ezfc_vars.form_submit_error);
			_this.submit_cancel($form);
				
			return false;
		});
	};

	/**
		external values
	**/
	this.calculate_get_external_values = function(form, form_id, el_object, el_type) {
		if (!el_object) return;
		
		var value_external_element = el_object.data("value_external");
		var value_external_listen  = el_object.data("value_external_listen");

		// only do it once if listen is disabled
		if (this.external_listeners[form_id][value_external_element] && !value_external_listen) return;

		if (value_external_element && $(value_external_element).length > 0) {
			// get external value
			var value_external;

			if ($(value_external_element).is("input[type='radio']")) {
				value_external = $(value_external_element).find(":checked").val();
			}
			else if ($(value_external_element).is("input, input[type='text'], textarea")) {
				value_external = $(value_external_element).val();
			}
			else if ($(value_external_element).is("select")) {
				value_external = $(value_external_element).find(":selected").text();
			}
			else {
				value_external = $(value_external_element).text();
			}

			// set external value
			if (el_type == "input" || el_type == "numbers" || el_type == "subtotal") {
				el_object.find("input").val(value_external);
			}
			else if (el_type == "dropdown") {
				el_object.find(":selected").removeAttr("selected");
				el_object.find("option[value='" + value_external + "']").attr("selected", "selected");
			}
			else if (el_type == "radio") {
				el_object.find(":checked").removeAttr("checked");
				el_object.find("input[value='" + value_external + "']").attr("checked", "checked");
			}
			else if (el_type == "checkbox") {
				el_object.find(":checked").removeAttr("checked");
				el_object.find("input[value='" + value_external + "']").attr("checked", "checked");
			}
			else if (el_type == "textfield") {
				el_object.find("textarea").val(value_external);
			}

			// set event listener
			if (!this.external_listeners[form_id][value_external_element]) {
				this.external_listeners[form_id][value_external_element] = 1;

				$(value_external_element).on("change keyup", function() {
					_this.set_price($(form));
				});
			}
		}
	};


	/**
		conditionals

		global conditionals override element_id as conditional object
	**/
	this.calculate_conditionals = function(form, form_id, element_id, el_type, is_global) {
		var element  = this.get_element_object(element_id);
		var $element = this.get_element_dom(element_id);

		// check if conditionals exist
		if ((!element_id || !element.hasOwnProperty("conditional")) && !is_global) return;
		// conditional rows
		var cond = element.conditional;
		if (is_global) cond = element_id;

		// invalid conditional
		if (!cond) return;

		// value of this element (but beware of is_number due to text values)
		var el_value  = this.get_value_from_element($element, null, element.is_number == 0);
		var el_factor = this.get_element_object_value(element, "factor", 1);

		// go through all conditionals
		for (var ic in cond.action) {
			var action = cond.action[ic];

			if (!action || action == 0) continue;

			// get conditional target element
			var $cond_target;
			var cond_target_element_id = cond.target[ic];
			var cond_target_element = _this.get_element_object(cond_target_element_id);

			if (cond.target[ic] == "submit_button" || action == "redirect") {
				$cond_target = $(form).find(".ezfc-submit");
			}
			else if (cond.target[ic] == "price") {
				$cond_target = $(form).find(".ezfc-price-wrapper-element");
			}
			else {
				$cond_target = _this.get_element_dom(cond_target_element_id);
			}

			// no target element found
			if (!$cond_target || ($cond_target.length < 1 && cond.redirects.length < 1)) continue;

			// check if raw value should be used
			if (cond.use_factor[ic] == 1) {
				el_factor = parseFloat(el_factor);
				if (!isNaN(el_factor)) {
					el_value *= el_factor;
				}
			}

			// chaining
			var conditional_chain = [ { operator: cond.operator[ic], value: cond.values[ic], compare_target: _this.get_object_value(cond, "compare_value_first", "", ic) } ];

			if (typeof cond.chain !== "undefined" && typeof cond.chain[ic] !== "undefined" && typeof cond.chain[ic].operator) {
				$.each(cond.chain[ic].operator, function(cn, operator_chain) {
					conditional_chain.push({ operator: cond.chain[ic].operator[cn], value: cond.chain[ic].value[cn], compare_target: cond.chain[ic].compare_target[cn] });
				});
			}

			/**
				check all conditional chains
			**/
			var do_action       = false;
			var do_action_count = 0; // for special checks
			var conditional_chain_check_length = 0;

			$.each(conditional_chain, function(chain_index, chain_row) {
				if (chain_row.operator == "") return;

				conditional_chain_check_length++;

				var do_action_index    = false; // TODO
				var cond_custom_value  = chain_row.value;
				var cond_value_min_max = [];

				var tmp_compare_value = el_value;
				var compare_target_element = element;
				// get value from compare target
				if (chain_row.compare_target != 0 && chain_row.compare_target != "") {
					tmp_compare_value = _this.get_value_from_element(false, chain_row.compare_target);
					compare_target_element = _this.get_element_object(chain_row.compare_target);
				}

				// override compare value with element's value
				if (chain_row.value == "__self__") {
					chain_row.value = el_value;
				}

				// only parse floats if value is a number
				if (element.is_number == 1 && $.inArray(chain_row.operator, conditional_value_parse_exceptions_array) === -1 && compare_target_element.type !== "datepicker") {
					cond_custom_value = parseFloat(chain_row.value);
				}
				else {
					cond_custom_value = chain_row.value;
				}

				// check for between-operator exception
				if (chain_row.operator == "between" || chain_row.operator == "not_between") {
					cond_value_min_max = chain_row.value.split(":");

					if (cond_value_min_max.length > 1 && compare_target_element.type !== "datepicker") {
						cond_value_min_max[0] = parseFloat(cond_value_min_max[0]);
						cond_value_min_max[1] = parseFloat(cond_value_min_max[1]);
					}
				}

				// datepicker
				if (compare_target_element.type === "datepicker") {
					var target_element_child_input = _this.get_element_child_input_dom(compare_target_element.id);
					if (!target_element_child_input) return;

					// get date from datepicker
					var tmp_compare_value_date = $(target_element_child_input).datepicker("getDate");
					// check for valid date
					if (_this.is_valid_date(tmp_compare_value_date)) {
						tmp_compare_value_date.setHours(0, 0, 0, 0);
						tmp_compare_value = tmp_compare_value_date.getTime();
					}

					// set compare date for separated dates
					if (cond_value_min_max.length > 1) {
						cond_value_min_max = [
							new Date(cond_value_min_max[0]),
							new Date(cond_value_min_max[1]),
						];

						if (_this.is_valid_date(cond_value_min_max[0])) cond_value_min_max[0].setHours(0, 0, 0, 0);
						if (_this.is_valid_date(cond_value_min_max[1])) cond_value_min_max[1].setHours(0, 0, 0, 0);

					}
					// set compare date
					else {
						var cond_custom_value_date = new Date(cond_custom_value);
						cond_custom_value_date.setHours(0, 0, 0, 0);
						cond_custom_value = cond_custom_value_date.getTime();
					}
				}

				switch (chain_row.operator) {
					case "gr": do_action_index = tmp_compare_value > cond_custom_value;
					break;
					case "gre": do_action_index = tmp_compare_value >= cond_custom_value;
					break;

					case "less": do_action_index = tmp_compare_value < cond_custom_value;
					break;
					case "lesse": do_action_index = tmp_compare_value <= cond_custom_value;
					break;

					case "equals":
						if (el_type == "input") {
							do_action_index = cond_custom_value.toLowerCase()==$cond_target.val().toLowerCase();
						}
						else {
							do_action_index = tmp_compare_value == cond_custom_value;
						}
					break;

					case "between":
						if (cond_value_min_max.length < 2) {
							do_action_index = false;
						}
						else {
							do_action_index = (tmp_compare_value >= cond_value_min_max[0] && tmp_compare_value <= cond_value_min_max[1]);
						}
					break;

					case "not_between":
						if (cond_value_min_max.length < 2) {
							do_action_index = false;
						}
						else {
							do_action_index = (tmp_compare_value < cond_value_min_max[0] || tmp_compare_value > cond_value_min_max[1]);
						}
					break;

					case "not":
						if (cond_value_min_max.length < 2) {
							do_action_index = tmp_compare_value != cond_custom_value;
						}
						else {
							do_action_index = (tmp_compare_value < cond_value_min_max[0] && tmp_compare_value > cond_value_min_max[1]);
						}
					break;

					case "hidden": do_action_index = !_this.element_is_visible(element_id);
					break;

					case "visible": do_action_index = _this.element_is_visible(element_id);
					break;

					case "mod0": do_action_index = tmp_compare_value > 0 && (tmp_compare_value % cond_custom_value) == 0;
					break;
					case "mod1": do_action_index = tmp_compare_value > 0 && (tmp_compare_value % cond_custom_value) != 0;
					break;

					case "bit_and": do_action_index = tmp_compare_value & cond_custom_value;
					break;

					case "bit_or": do_action_index = tmp_compare_value | cond_custom_value;
					break;

					case "empty":
						if (typeof tmp_compare_value === "undefined") {
							do_action_index = true;
						}
						if (typeof tmp_compare_value === "number") {
							do_action_index = isNaN(tmp_compare_value);
						}
						else {
							do_action_index = tmp_compare_value.length < 1;
						}
					break;

					case "notempty":
						if (typeof tmp_compare_value === "undefined") {
							do_action_index = false;
						}
						else if (typeof tmp_compare_value === "number") {
							do_action_index = !isNaN(tmp_compare_value);
						}
						else {
							do_action_index = tmp_compare_value.length > 0;
						}
					break;

					case "contains":
					case "starts_with":
					case "ends_with":
						if (typeof tmp_compare_value === "undefined") {
							do_action_index = false;
						}
						else {
							var str_compare_value = $.trim(String(cond_custom_value));
							var str_compare_check = $.trim(String(tmp_compare_value));

							if (chain_row.operator == "contains")         do_action_index = str_compare_check.includes(str_compare_value);
							else if (chain_row.operator == "starts_with") do_action_index = str_compare_check.startsWith(str_compare_value);
							else if (chain_row.operator == "ends_with")   do_action_index = str_compare_check.endsWith(str_compare_value);
						}
					break;

					case "in":
						if (typeof tmp_compare_value === "undefined") {
							do_action_index = false;
						}
						else {
							do_action_index = _this.conditional_check_multiple_options(cond_custom_value, tmp_compare_value);
						}
					break;

					case "not_in":
						if (typeof tmp_compare_value === "undefined") {
							do_action_index = false;
						}
						else {
							do_action_index = !_this.conditional_check_multiple_options(cond_custom_value, tmp_compare_value);
						}
					break;

					case "once":
						do_action_index = true;

						if (typeof _this.conditional_once[form_id][element_id] === "undefined") {
							_this.conditional_once[form_id][element_id] = [];
						}
						if (typeof _this.conditional_once[form_id][element_id][ic] === "undefined") {
							_this.conditional_once[form_id][element_id][ic] = [];
						}

						if (typeof _this.conditional_once[form_id][element_id][ic][chain_index] === "undefined") {
							_this.conditional_once[form_id][element_id][ic][chain_index] = 1;
						}
						else {
							do_action_index = false;
						}
					break;

					case "selected":
					case "selected_index":
					case "selected_id":
					case "selected_count":
					case "selected_count_gt":
					case "selected_count_lt":
						do_action_index = false;

						// get array value
						var tmp_return_value = "value";
						if (chain_row.operator == "selected_index") tmp_return_value = "index";
						else if (chain_row.operator == "selected_id") tmp_return_value = "option_id";
						else if (chain_row.operator == "selected_count" || chain_row.operator == "selected_count_gt" || chain_row.operator == "selected_count_lt") tmp_return_value = "count";

						var tmp_value;
						if (chain_row.compare_target != 0 && chain_row.compare_target != "") {
							tmp_value = _this.get_value_from_element(false, chain_row.compare_target, true, false, { return_array: true, return_value: tmp_return_value });
						}
						else {
							tmp_value = _this.get_value_from_element($element, null, true, false, { return_array: true, return_value: tmp_return_value });
						}

						if (typeof tmp_value === "object") {
							do_action_index = _this.conditional_check_multiple_options(cond_custom_value, tmp_value);
						}
						// other compare method (sum, count etc.)
						else {
							if (chain_row.operator == "selected_count") do_action_index = tmp_value == cond_custom_value;
							else if (chain_row.operator == "selected_count_gt") do_action_index = tmp_value > cond_custom_value;
							else if (chain_row.operator == "selected_count_lt") do_action_index = tmp_value < cond_custom_value;
							else {
								//do_action_index = tmp_value == cond_custom_value;
								do_action_index = _this.conditional_check_multiple_options(cond_custom_value, tmp_value);
							}
						}
					break;

					case "not_selected":
					case "not_selected_index":
					case "not_selected_id":
					case "not_selected_count":
						do_action_index = true;

						// get array value
						var tmp_return_value = "value";
						if (chain_row.operator == "not_selected_index") tmp_return_value = "index";
						else if (chain_row.operator == "not_selected_id") tmp_return_value = "option_id";
						else if (chain_row.operator == "not_selected_count") tmp_return_value = "count";

						var tmp_value;
						if (chain_row.compare_target != 0 && chain_row.compare_target != "") {
							tmp_value = _this.get_value_from_element(false, chain_row.compare_target, true, false, { return_array: true, return_value: tmp_return_value });
						}
						else {
							tmp_value = _this.get_value_from_element($element, null, true, false, { return_array: true, return_value: tmp_return_value });
						}

						do_action_index = !_this.conditional_check_multiple_options(cond_custom_value, tmp_value);
					break;

					case "focus":
						if ($element.find(".ezfc-slider").length > 0) {
							do_action_index = $element.find("input").is(":focus") || $element.find(".ui-state-active").length > 0;
						}
						else {
							do_action_index = $element.find("input").is(":focus");
						}
					break;
					case "blur":
						if ($element.find(".ezfc-slider").length > 0) {
							do_action_index = !$element.find("input").is(":focus") && $element.find(".ui-state-active").length < 1;
						}
						else {
							do_action_index = !$element.find("input").is(":focus");
						}
					break;

					case "always":
						do_action_index = true;
					break;

					// step equals custom value
					case "step_equals":
						var current_step = _this.current_steps[form_id];
						do_action_index = current_step == cond_custom_value;
					break;
					// step is greater than custom value
					case "step_gt":
						var current_step = _this.current_steps[form_id];
						do_action_index = current_step > cond_custom_value;
					break;
					// step is lower than custom value
					case "step_lt":
						var current_step = _this.current_steps[form_id];
						do_action_index = current_step < cond_custom_value;
					break;

					// is activated
					case "calculate_enabled":
						do_action_index = _this.get_element_object_value(element, "calculate_enabled", 1) == 1;
					break;
					// is deactivated
					case "calculate_disabled":
						do_action_index = _this.get_element_object_value(element, "calculate_enabled", 1) == 0;
					break;

					// element was triggered
					case "triggered":
						if (!compare_target_element) do_action_index = false;
						else do_action_index = _this.element_trigger_id == compare_target_element.id;
					break;

					default:
						do_action_index = false;
					break;						
				}

				// at least one condition needs to be true (i.e. row OR operator)
				if (typeof cond.row_operator[ic] !== "undefined" && cond.row_operator[ic] == 1) {
					if (do_action_index) {
						do_action_count++;
						do_action = true;
						return false;
					}
				}
				// all conditions need to be true (i.e. row AND operator)
				else {
					if (!do_action_index) return;

					do_action_count++;
				}
			});

			// check if all conditions are true
			if (do_action_count > 0 && do_action_count == conditional_chain_check_length) {
				do_action = true;
			}

			// conditional actions
			var js_action, js_counter_action;
			// when cond_notoggle_element is true, the opposite action will not be executed
			var cond_notoggle_element = cond.notoggle[ic];
			// target element type
			var cond_target_type = $cond_target.data("element");
			// target value
			var tmp_custom_value = cond.target_value[ic];
			// replace __self__ with own value
			tmp_custom_value = tmp_custom_value.replace(/__self__/gi, el_value);

			// set $cond_target to all direct child elements when it's a group
			if (cond_target_type == "group") {
				$cond_target.push($($cond_target).find("> .ezfc-custom-element"));
			}

			// set values
			if (action == "set" && do_action) {
				var tmp_custom_value_inline_math = _this.check_inline_math(tmp_custom_value, $cond_target);//todo normalize

				if (cond_target_type == "input" || cond_target_type == "hidden" || cond_target_type == "numbers" || cond_target_type == "subtotal" || cond_target_type == "set") {
					_this.set_element_object_value(cond_target_element_id, "current_value", tmp_custom_value_inline_math);
					_this.set_element_object_value(cond_target_element_id, "current_value_forced", tmp_custom_value_inline_math);

					// write formatted value into the input field
					//var tmp_custom_value_normalized = _this.format_price(form_id, tmp_custom_value_inline_math, null, null, null, true);
					//$cond_target.find("input").val(tmp_custom_value_normalized).trigger("blur"); // recursive stack error?
					//$cond_target.find("input").val(tmp_custom_value_normalized).trigger("ezfc_blur_trigger");
					//$cond_target.find("input").val(tmp_custom_value_inline_math).trigger("ezfc_blur_trigger");

					$cond_target.find("input").val(tmp_custom_value_inline_math).trigger("blur");
				}
				else if (cond_target_type == "dropdown") {
					$cond_target.find(":selected").removeAttr("selected");
					$cond_target.find("option[data-value='" + tmp_custom_value_inline_math + "']").prop("selected", "selected");
				}
				else if (cond_target_type == "radio") {
					_this.radio_change_state($cond_target.find("input[data-value='" + tmp_custom_value_inline_math + "']"), true, true);
				}
				else if (cond_target_type == "checkbox") {
					_this.checkbox_change_state($cond_target.find("input[data-value='" + tmp_custom_value_inline_math + "']"), true, true);
				}
				else if (cond_target_type == "datepicker") {
					var $datepicker_element = $cond_target.find("input.hasDatepicker");
					if ($datepicker_element.length) {
						$datepicker_element.datepicker("setDate", tmp_custom_value);
					}
				}
				else if (cond_target_type == "daterange") {
					var $datepicker_element = $cond_target.find("input.hasDatepicker");
					if ($datepicker_element.length > 1) {
						var daterange_values = tmp_custom_value.split(";;");

						// set from
						if (typeof daterange_values[0] !== "undefined") $($datepicker_element[0]).datepicker("setDate", daterange_values[0]);
						// set to
						if (typeof daterange_values[1] !== "undefined") $($datepicker_element[1]).datepicker("setDate", daterange_values[1]);
					}
				}
				else if (cond_target_type == "image") {
					$cond_target.find("img").attr("src", tmp_custom_value);
				}
				else {
					$cond_target.text(tmp_custom_value_inline_math);
				}
			}
			// set factor
			else if (action == "set_factor" && do_action) {
				_this.set_element_object_value(cond_target_element_id, "factor", tmp_custom_value);
			}
			// select option
			else if (action == "select_option" && do_action) {
				var tmp_custom_values_array = tmp_custom_value.split("|");
				for (var i in tmp_custom_values_array) {
					var tmp_custom_value_index = tmp_custom_values_array[i];

					if (cond_target_type == "radio") {
						_this.radio_change_state($cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']"), true, true);
					}
					else if (cond_target_type == "checkbox") {
						_this.checkbox_change_state($cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']"), true, true);
					}
					else if (cond_target_type == "dropdown") {
						$cond_target.find(":selected").removeAttr("selected");
						$cond_target.find("option[data-optionid='" + tmp_custom_value_index + "']").prop("selected", "selected");
					}
				}
			} 
			// deselect option
			else if ((action == "deselect_option" || action == "deselect_all_options") && do_action) {
				var tmp_custom_values_array = tmp_custom_value.split("|");

				for (var i in tmp_custom_values_array) {
					var tmp_custom_value_index = tmp_custom_values_array[i];
					var find_input_selector = "input[data-optionid='" + tmp_custom_value_index + "']";
					if (action == "deselect_all_options") {
						find_input_selector = ":selected, :checked";
					}

					if (cond_target_type == "radio") {
						_this.radio_change_state($cond_target.find(find_input_selector), false, true);
					}
					else if (cond_target_type == "checkbox") {
						_this.checkbox_change_state($cond_target.find(find_input_selector), false, true);
					}
					else if (cond_target_type == "dropdown") {
						$cond_target.find(":selected").removeAttr("selected");
					}
				}
			}
			// show option
			else if (action == "show_option") {
				var tmp_custom_values_array = tmp_custom_value.split("|");

				for (var i in tmp_custom_values_array) {
					var tmp_custom_value_index = tmp_custom_values_array[i];

					if (do_action) {
						if (cond_target_type == "radio" || cond_target_type == "checkbox") {
							$cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']").closest(".ezfc-element-single-option-container").show();
						}
						else if (cond_target_type == "dropdown") {
							$cond_target.find("option[data-optionid='" + tmp_custom_value_index + "']").removeAttr("hidden");
						}
					}
					// hide option
					else {
						if (cond_target_type == "radio") {
							_this.radio_change_state($cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']"), false, true);
							$cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']").closest(".ezfc-element-single-option-container").hide();
						}
						else if (cond_target_type == "checkbox") {
							_this.checkbox_change_state($cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']"), false, true);
							$cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']").closest(".ezfc-element-single-option-container").hide();
						}
						else if (cond_target_type == "dropdown") {
							$cond_target.find(":selected").removeAttr("selected");
							$cond_target.find("option[data-optionid='" + tmp_custom_value_index + "']").attr("hidden", true);
						}
					}
				}
			} 
			// hide option
			else if (action == "hide_option") {
				var tmp_custom_values_array = tmp_custom_value.split("|");

				for (var i in tmp_custom_values_array) {
					var tmp_custom_value_index = tmp_custom_values_array[i];

					if (do_action) {
						if (cond_target_type == "radio") {
							_this.radio_change_state($cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']"), false, true);
							$cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']").closest(".ezfc-element-single-option-container").hide();
						}
						else if (cond_target_type == "checkbox") {
							_this.checkbox_change_state($cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']"), false, true);
							$cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']").closest(".ezfc-element-single-option-container").hide();
						}
						else if (cond_target_type == "dropdown") {
							$cond_target.find("option[data-optionid='" + tmp_custom_value_index + "']").attr("hidden", true).removeAttr("selected");
						}
					}
					// show option
					else {
						if (cond_target_type == "radio" || cond_target_type == "checkbox") {
							$cond_target.find("input[data-optionid='" + tmp_custom_value_index + "']").closest(".ezfc-element-single-option-container").show();
						}
						else if (cond_target_type == "dropdown") {
							$cond_target.find("option[data-optionid='" + tmp_custom_value_index + "']").removeAttr("hidden");
						}
					}
				}
			}
			// activate element
			else if (action == "activate") {
				// activate
				if (do_action) {
					// submit button
					if (cond_target_type == "submit") {
						$cond_target.prop("disabled", false);
					}
					// group
					else if (cond_target_type == "group") {
						var group_children = _this.get_group_children(cond_target_element_id);
						_this.set_element_list_object_value(group_children, "calculate_enabled", 1);
					}
					// element
					else {
						_this.set_element_object_value(cond_target_element_id, "calculate_enabled", 1);
					}
				}
				// deactivate
				else if (cond_notoggle_element != 1) {
					// submit button
					if (cond_target_type == "submit") {
						$cond_target.prop("disabled", true);
					}
					// group
					else if (cond_target_type == "group") {
						var group_children = _this.get_group_children(cond_target_element_id);
						_this.set_element_list_object_value(group_children, "calculate_enabled", 0);
					}
					// element
					else {
						_this.set_element_object_value(cond_target_element_id, "calculate_enabled", 0);
					}
				}
			}
			// activate option
			else if (action == "activate_option") {
				var find_option_id = "[data-optionid='" + cond.option_index_value[ic] + "']";

				// activate
				if (do_action) {
					$cond_target.find(find_option_id).prop("disabled", false).removeClass("force-disabled");
				}
				// deactivate
				else {
					$cond_target.find(find_option_id).prop("disabled", true).addClass("force-disabled");
				}
			}
			// deactivate element
			else if (action == "deactivate") {
				// deactivate
				if (do_action) {
					// submit button
					if (cond_target_type == "submit") {
						$cond_target.prop("disabled", true);
					}
					// group
					else if (cond_target_type == "group") {
						var group_children = _this.get_group_children(cond_target_element_id);
						_this.set_element_list_object_value(group_children, "calculate_enabled", 0);
					}
					// element
					else {
						_this.set_element_object_value(cond_target_element_id, "calculate_enabled", 0);
					}
				}
				// activate
				else if (cond_notoggle_element != 1) {
					// submit button
					if (cond_target_type == "submit") {
						$cond_target.prop("disabled", false);
					}
					// group
					else if (cond_target_type == "group") {
						var group_children = _this.get_group_children(cond_target_element_id);
						_this.set_element_list_object_value(group_children, "calculate_enabled", 1);
					}
					// element
					else {
						_this.set_element_object_value(cond_target_element_id, "calculate_enabled", 1);
					}
				}
			}
			// deactivate option
			else if (action == "deactivate_option") {
				var find_option_index = "[data-optionid='" + cond.option_index_value[ic] + "']";

				// activate
				if (do_action) {
					$cond_target.find(find_option_index).prop("disabled", true).addClass("force-disabled");
				}
				// deactivate
				else {
					$cond_target.find(find_option_index).prop("disabled", false).removeClass("force-disabled");
				}
			}
			// load form
			else if (action == "redirect" && do_action) {
				// set message
				var message_wrapper = $(form).parents(".ezfc-wrapper").find(".ezfc-message");
				message_wrapper.text(_this.form_vars[form_id].redirect_text).fadeIn();

				// hide the form
				$(form).fadeOut();

				setTimeout(function() {
					window.location.href = cond.redirects[ic];
				}, _this.form_vars[form_id].redirect_timer * 1000);
			}
			// steps
			else if ((action == "step_goto" || action == "step_prev" || action == "step_next") && do_action) {
				_this.set_step_action(form_id, action, cond.target[ic]);
			}
			// set min/max
			else if ((action == "set_min" || action == "set_max") && do_action) {
				var action_attr = action == "set_min" ? "min" : "max";
				var action_attr_opposite = action == "set_min" ? "max" : "min";

				var $cond_target_input = $cond_target.find(":input");
				var slider_custom_value = parseFloat(tmp_custom_value);

				// check min/max first
				var slider_min = $cond_target_input.data("min");
				var slider_max = $cond_target_input.data("max");
				if (action == "set_min" && slider_custom_value > slider_max) continue;
				if (action == "set_max" && slider_custom_value < slider_min) continue;

				$cond_target_input.data(action_attr, slider_custom_value).trigger("ezfc_blur_trigger");

				// check for slider
				if ($cond_target_input.hasClass("ezfc-slider")) {
					var $cond_target_slider = $cond_target.find(".ezfc-slider-element");

					if (typeof $cond_target_slider.slider !== "undefined" && $cond_target_slider.slider("instance") !== undefined) {
						$cond_target_slider.slider("option", action_attr, slider_custom_value);
					}
				}
			}
			// set min/max selectable
			else if (action == "set_min_selectable" && do_action) {
				$cond_target.attr("data-min_selectable", tmp_custom_value);
				_this.checkbox_change($cond_target);
			}
			else if (action == "set_max_selectable" && do_action) {
				$cond_target.attr("data-max_selectable", tmp_custom_value);
				_this.checkbox_change($cond_target);
			}
			// add / remove class
			else if (action == "add_class") {
				var tmp_action = do_action ? "addClass" : "removeClass";
				$cond_target[tmp_action](tmp_custom_value);
			}
			else if (action == "remove_class") {
				var tmp_action = do_action ? "removeClass" : "addClass";
				$cond_target[tmp_action](tmp_custom_value);
			}
			// color
			else if (action == "set_color" && do_action) {
				$cond_target.find("input, select, .ezfc-price, .ezfc-element-checkbox-text").css("color", tmp_custom_value);

				if (cond_target_type == "submit") $cond_target.css("color", tmp_custom_value);
			}
			// enable calc routine index
			else if (action == "calc_routine_enable") {
				var calculation_routine_id = parseInt(tmp_custom_value);

				if (!isNaN(calculation_routine_id)) {
					calculation_routine_id -= 1; // due to backend index display

					if (cond_target_element.hasOwnProperty("calculate_routine") && typeof cond_target_element.calculate_routine[calculation_routine_id] === "object") {
						cond_target_element.calculate_routine[calculation_routine_id].activated = do_action;
					}
				}
			}
			// enable calc routine name
			else if (action == "calc_routine_enable_name") {
				if (tmp_custom_value.length > 0 && cond_target_element.hasOwnProperty("calculate_routine")) {
					// find calculation routine
					var calculation_routines = _this.get_calculation_routine_by_name(cond_target_element, tmp_custom_value);

					for (var cri in calculation_routines) {
						if (typeof cond_target_element.calculate_routine[calculation_routines[cri]] === "object") {
							cond_target_element.calculate_routine[calculation_routines[cri]].activated = do_action;
						}
					}
				}
			}
			// disable calc routine index
			else if (action == "calc_routine_disable") {
				var calculation_routine_id = parseInt(tmp_custom_value);

				if (!isNaN(calculation_routine_id)) {
					calculation_routine_id -= 1; // due to backend index display

					if (cond_target_element.hasOwnProperty("calculate_routine") && typeof cond_target_element.calculate_routine[calculation_routine_id] === "object") {
						cond_target_element.calculate_routine[calculation_routine_id].activated = !do_action;
					}
				}
			}
			// disable calc routine name
			else if (action == "calc_routine_disable_name") {
				if (tmp_custom_value.length > 0 && cond_target_element.hasOwnProperty("calculate_routine")) {
					// find calculation routine
					var calculation_routines = _this.get_calculation_routine_by_name(cond_target_element, tmp_custom_value);

					for (var cri in calculation_routines) {
						if (typeof cond_target_element.calculate_routine[calculation_routines[cri]] === "object") {
							cond_target_element.calculate_routine[calculation_routines[cri]].activated = !do_action;
						}
					}
				}
			}
			// show / hide elements
			else {
				_this.call_conditional_action(do_action, action, $cond_target, cond_target_element_id, cond_notoggle_element);
			}
		}
	};

	this.conditional_actions = {
		class_hidden: "ezfc-hidden ezfc-custom-hidden",
		class_visible: "ezfc-fade-in",

		show: function(do_action, $target_element, element, notoggle) {
			$target_element.addClass(_this.conditional_actions.class_visible).removeClass(_this.conditional_actions.class_hidden);

			if (element && element.type == "group") {
				$target_element.find(".ezfc-element").addClass(_this.conditional_actions.class_visible).removeClass(_this.conditional_actions.class_hidden);
			}

			if (!do_action && notoggle != 1) {
				_this.conditional_actions.hide(true, $target_element, element);
			}
		},

		hide: function(do_action, $target_element, element, notoggle) {
			$target_element.addClass(_this.conditional_actions.class_hidden).removeClass(_this.conditional_actions.class_visible);

			if (element && element.type == "group") {
				$target_element.find(".ezfc-element").addClass(_this.conditional_actions.class_hidden).removeClass(_this.conditional_actions.class_visible);
			}

			if (!do_action && notoggle != 1) {
				_this.conditional_actions.show(true, $target_element, element);
			}
		}
	};

	// call conditional action
	this.call_conditional_action = function(do_action, action, $target_element, target_element_id, notoggle) {
		// invalid action
		if (!this.conditional_actions.hasOwnProperty(action)) return;

		var element;
		if (target_element_id == "submit_button") {
			element = null;
		}
		else {
			element = this.get_element_object(target_element_id);
			if (!element) return;
		}

		this.conditional_actions[action](do_action, $target_element, element, notoggle);
	};

	/**
		calculate single element
	**/
	this.calculate_element = function(form_id, element_id, loop_price) {
		if (!form_id && element_id) form_id = $("#ezfc_element-" + element_id).closest(".ezfc-form").data("id");

		var $element = this.get_element_dom(element_id);
		var element  = this.get_element_object(element_id);
		var parent_id = this.get_repeatable_parent_id(element_id);

		// form not yet initialized
		if (typeof element === "undefined") return;

		// check if element exist
		if (!$element) {
			this.debug_message("Unable to find element #" + element_id, 2);
			return;
		}

		// overwrite form ID if parent ID exists
		if (parent_id) {
			form_id = this.get_element_object_value(parent_id, "form_id", 0);
		}

		var calc_rows       = typeof element.calculate === "undefined" ? [] : element.calculate;
		var calc_enabled    = element.calculate_enabled;
		var add_to_price    = element.add_to_price;
		var el_type         = element.type;
		var form_has_steps  = _this.form_vars[form_id].has_steps;
		var overwrite_price = element.overwrite_price;
		var price           = 0;
		var value           = _this.get_value_from_element($element, null, false);

		if (el_type == "subtotal" || el_type == "custom_calculation" || el_type == "extension") {
			price = loop_price;
		}

		if (el_type == "extension") {
			calc_rows = [];
		}

		var calculate_when_zero = this.get_element_object_value(element, "calculate_when_zero", 1);
		// do not calculate when price is 0
		if (calculate_when_zero == 0 && price == 0) return;
	
		// dropdowns / radios / checkboxes could contain more values
		var calc_list = $element.find(".ezfc-element-numbers, .ezfc-element-input-hidden, .ezfc-element-subtotal, .ezfc-element-daterange-container, .ezfc-element-set, .ezfc-element-extension, :selected, :checked, .ezfc-element-custom-calculation, .ezfc-element-table_order");

		// these operators do not need any target or value
		var operator_no_check = ["ceil", "floor", "round", "abs", "subtotal"];

		$(calc_list).each(function(cl, cl_object) {
			// no calculation rows
			if ((typeof calc_rows === "undefined" || calc_rows.length == 0) && el_type != "custom_calculation" && el_type != "repeatable_form") return;

			// skip when calculation is disabled for hidden elements
			var check_visible = _this.element_is_visible(element_id);

			if (!check_visible && element.calculate_when_hidden == 0 && el_type != "hidden" && el_type != "custom_calculation") {
				_this.add_debug_info("calculate", $element, "Skipped as element is hidden and calculate_when_hidden is not enabled.");
				return;
			}

			// no target or values to calculate with were found. skip for subtotals / hidden.
			if ((!calc_enabled || calc_enabled == 0) &&
				!calc_rows.targets &&
				!calc_rows.values &&
				el_type != "set" &&
				el_type != "subtotal" &&
				el_type != "hidden" &&
				el_type != "extension" &&
				el_type != "custom_calculation" &&
				el_type != "repeatable_form") {
				_this.add_debug_info("calculate", $element, "No target or values were found to calculate with. Subtotal, Hidden and Set elements are skipped.");
				return;
			}

			// check if calculation is enabled for _this element
			if ((!calc_enabled || calc_enabled == 0) && el_type != "custom_calculation") {
				_this.add_debug_info("calculate", $element, "Calculation is disabled.");
				return;
			}

			var factor       = parseFloat(_this.get_element_object_value(element, "factor", 1));
			var value_raw    = $(cl_object).val();
			var value_pct    = value / 100;
			var value_is_pct = false;

			// default values
			if (!value || isNaN(value)) value = 0;
			if ((!factor || isNaN(factor)) && factor !== 0) factor = 1;

			// set addprice to value at first
			var addPrice = value;

			// basic calculations
			switch (el_type) {
				case "numbers":
				case "extension":
				case "hidden":
					addPrice = value;
				break;

				case "dropdown":
				case "radio":
				case "checkbox":
					addPrice = parseFloat($(cl_object).data("value"));
					if (isNaN(addPrice)) addPrice = 0;
				break;

				case "subtotal":
					addPrice = loop_price;
				break;

				case "daterange":
					var tmp_target_value = [
						// from
						$(cl_object).find(".ezfc-element-daterange-from").datepicker("getDate"),
						// to
						$(cl_object).find(".ezfc-element-daterange-to").datepicker("getDate")
					];
					
					addPrice = _this.jqueryui_date_diff(tmp_target_value[0], tmp_target_value[1], _this.get_element_object_value(element, "workdays_only", 0) == 1, element) * factor;
				break;

				// custom calculation function
				case "custom_calculation":
					var function_name = $($element).find(".ezfc-element-custom-calculation").data("function");

					try {
						addPrice = window[function_name](price);
					}
					catch (err) {
						_this.add_debug_info("custom_calculation", $element, "--- Custom Calculation Error ---\n" + err);
						addPrice = 0;
					}

					if (calc_enabled) {
						addPrice = parseFloat(addPrice);
					}

					$($element).find(".ezfc-element-custom-calculation-input").val(addPrice);

					// improve performance here
					if (ezfc_vars.debug_mode == 2) {
						var function_text = $($element).find(".ezfc-element-custom-calculation script").text();
						_this.add_debug_info("custom_calculation", $element, "custom_calculation:\n" + function_text);
					}
				break;

				case "table_order":
					var tmp_total = 0;
					var tmp_element = _this.get_element_object(element_id);
					var table_order_loop_func = _this.get_element_object_value(tmp_element, "table_order_loop_function", "");

					$.each(tmp_element.table_order, function(i, row) {
						var $input_id = $element.find(".ezfc-element-table_order-row[data-row='" + i + "'] .ezfc-element-table_order-quantity-input");

						var qty = _this.normalize_value($input_id.val());
						var row_add_text = "";
						var row_total = parseFloat(row.value) * qty;

						// call custom func
						var table_order_loop_func_result;
						if (typeof window[table_order_loop_func] === "function") {
							table_order_loop_func_result = window[table_order_loop_func](row_total, qty, i, form_id, element_id, tmp_element);
						}

						var row_total_before = row_total;
						if (typeof table_order_loop_func_result === "object") {
							row_total = _this.get_element_object_value(table_order_loop_func_result, "value", row_total);
							row_add_text = _this.get_element_object_value(table_order_loop_func_result, "text", "");
						}

						var row_total_format_value = row_total;
						// value has changed, use old value as the "before" price
						if (row_total_before != row_total) {
							row_total_format_value = row_total_before;
						}

						var row_total_formatted = _this.format_price(form_id, row_total_format_value, null, null, tmp_element.is_currency);
						row_total_formatted    += row_add_text;

						tmp_total += row_total;

						$element.find(".ezfc-element-table_order-row[data-row='" + i + "'] .ezfc-element-table_order-subtotal").html(row_total_formatted);
					});

					addPrice = tmp_total;
				break;
			}

			// percent calculation
			if (value_is_pct) {
				addPrice = price * value * 100;
			}

			var calc_rows_loop = [];
			if (typeof calc_rows[0] !== "undefined") {
				calc_rows_loop = calc_rows.slice(0);
			}

			// calculation routines
			if (element.hasOwnProperty("calculate_routine")) {
				for (var cr_index in element.calculate_routine) {
					if (element.calculate_routine[cr_index].activated && typeof calc_rows[0] !== "undefined") {
						// add to calc rows
						for (var cr_row_index in element.calculate_routine[cr_index].calculation_rows) {
							calc_rows_loop.push(element.calculate_routine[cr_index].calculation_rows[cr_row_index]);
						}
					}
				}
			}

			// check if any advanced calculations are present
			if (typeof calc_rows_loop[0] !== "undefined") {
				// transfer "open" bracket data to "close" bracket
				for (var c_open in calc_rows) {
					if (calc_rows[c_open].target == "__open__" && typeof calc_rows[c_open].reference_index === "undefined") {
						// check for valid prio
						calc_rows[c_open].prio = parseInt(calc_rows[c_open].prio);
						if (isNaN(calc_rows[c_open].prio)) {
							calc_rows[c_open].prio = 0;
						}

						for (var c_close in calc_rows) {
							if (c_open == c_close) continue;

							calc_rows[c_close].prio = parseInt(calc_rows[c_close].prio);
							if (isNaN(calc_rows[c_close].prio)) {
								calc_rows[c_close].prio = 0;
							}

							// find next close bracket with the same priority as the open bracket
							if (calc_rows[c_close].target == "__close__" && calc_rows[c_open].prio == calc_rows[c_close].prio && typeof calc_rows[c_open].reference_index === "undefined" && typeof calc_rows[c_close].reference_index === "undefined") {
								calc_rows[c_close].operator        = calc_rows[c_open].operator;
								calc_rows[c_close].reference_index = c_open;
								calc_rows[c_open].reference_index  = c_close;
							}
						}
					}
				}

				// iterate through all operators elements
				$.each(calc_rows_loop, function(n, calc_row) {
					// no calculation operator
					if ((!calc_row.operator || calc_row.operator == 0) && calc_row.target != "__close__") {
						_this.add_debug_info("calculate", $element, "#" + n + ": No operator found here.");
						return;
					}

					// skip open bracket
					if (calc_row.target == "__open__") {
						calc_row.value = addPrice;
						addPrice = 0;
						return;
					}

					var $calc_target   = [];
					var target_element = [];
					// operator needs a target
					if ($.inArray(calc_row.operator, operator_no_check) === -1 && calc_row.target != "__open__" && calc_row.target != "__close__") {
						$calc_target   = _this.get_element_dom(calc_row.target);
						target_element = _this.get_element_object(calc_row.target);

						// skip hidden element only if calculate_when_hidden is false
						if (!_this.element_is_visible(calc_row.target) && target_element.calculate_when_hidden == 0) {
							_this.add_debug_info("calculate", $element, "#" + n + ": Skipping this element as it is conditionally hidden.");

							// set price to 0 or the previous subtotal price will be used (falsely)
							if (n == 0 && calc_row.operator == "equals") addPrice = 0;
							return;
						}
					}

					// custom value used when no target was found
					var calc_value = calc_row.value;

					// use value from target
					var target_value;
					var calc_target_id = 0;

					// target value is value of next close bracket
					if (calc_row.target == "__close__") {
						if (typeof calc_rows[calc_row.reference_index] === "undefined") return;

						target_value = addPrice;
						addPrice = calc_rows[calc_row.reference_index].value;
					}
					else {
						if ($calc_target.length > 0) {
							calc_target_id = target_element.id;

							if (typeof calc_row.use_calculated_target_value === "undefined") {
								calc_row.use_calculated_target_value = 0;
							}

							// raw value
							if (calc_row.use_calculated_target_value == 0) {
								//target_value = _this.get_value_from_element($calc_target, null, false);
								target_value = _this.get_value_from_element(null, calc_target_id, false);
							}
							// calculated target value with subtotal
							else if (calc_row.use_calculated_target_value == 1) {
								target_value = _this.get_target_subtotal_value(form_id, calc_target_id) + _this.get_calculated_element_value(form_id, calc_target_id);
							}
							// calculated target value without subtotal
							else if (calc_row.use_calculated_target_value == 2) {
								target_value = _this.get_calculated_element_value(form_id, calc_target_id);
							}
							// raw value without factor
							else if (calc_row.use_calculated_target_value == 3) {
								target_value = _this.get_value_from_element($calc_target, null, false, true);
							}
							// selected count
							else if (calc_row.use_calculated_target_value == 4) {
								target_value = _this.get_value_from_element($calc_target, null, true, false, { return_array: false, return_value: "count" });
							}
						}
						else if (calc_value != 0) {
							target_value = parseFloat(calc_value);
						}
					}

					if (!target_value || isNaN(target_value)) target_value = 0;
					// use element precision
					if (element.precision != "") {
						target_value = parseFloat(_this.roundTo(target_value, element.precision));
					}

					switch (calc_row.operator) {
						case "add":	addPrice += target_value;
						break;

						case "subtract": addPrice -= target_value;
						break;

						case "multiply": addPrice *= target_value;
						break;

						case "divide": 
							if (target_value == 0) {
								_this.add_debug_info("calculate", $element, "#" + n + ": Division by 0.");
								addPrice = 0;
							}
							else {
								addPrice /= target_value;

								// still necessary?
								if ($(cl_object).data("calculate_before") == "1") {
									overwrite_price = 1;
									addPrice = target_value / value;
								}
							}
						break;

						case "equals": addPrice = target_value;
						break;

						case "power": addPrice = Math.pow(addPrice, target_value);
						break;

						case "ceil": addPrice = Math.ceil(addPrice);
						break;

						case "floor": addPrice = Math.floor(addPrice);
						break;

						case "min": addPrice = Math.min(addPrice, target_value);
						break;

						case "max": addPrice = Math.max(addPrice, target_value);
						break;

						case "minmax":
							var minmax = String(calc_value).split(":");
							if (minmax.length < 2) return;

							addPrice = Math.max(Math.min(addPrice, minmax[1]), minmax[0]);
						break;

						case "round": addPrice = Math.round(addPrice);
						break;

						case "roundx": addPrice = _this.roundTo(addPrice, target_value);
						break;

						case "abs": addPrice = Math.abs(addPrice);
						break;

						case "subtotal": addPrice = loop_price;
						break;

						case "log":
							if (target_value == 0) return;
							addPrice = Math.log(target_value);
						break;
						case "log2":
							if (target_value == 0) return;
							addPrice = Math.log2(target_value);
						break;
						case "log10":
							if (target_value == 0) return;
							addPrice = Math.log10(target_value);
						break;

						case "sqrt": addPrice = Math.sqrt(addPrice);
						break;

						case "function":
							var function_name = _this.get_element_object_value(calc_row, "data", "");
							if (function_name.length && typeof window[function_name] === "function") {
								var function_price = parseFloat(window[function_name](addPrice, target_value));
								if (!isNaN(function_price)) {
									addPrice = function_price;
								}
							}
						break;
					}

					_this.add_debug_info("calculate", $element, "#" + n + ": operator = " + calc_row.operator + "\ntarget_value = " + target_value + "\ntarget_element = #" + calc_target_id + "\ncalc_value = " + calc_value + "\naddPrice = " + addPrice);
				});
			}

			// add calculated price to total price
			if (add_to_price == 1) {
				price += addPrice;
			}
			else if (add_to_price == 2) {
				price = addPrice;
			}

			// overwrite price
			if (overwrite_price == 1) {
				price = addPrice;
			}

			_this.add_debug_info("calculate", $element, "===\nprice = " + price + "\naddPrice = " + addPrice + "\nloop_price = "+ loop_price + "\nvalue = " + value + "\nfactor = " + factor);
		});

		// inline calculation
		if (typeof element.inline_calculation !== "undefined" && typeof window[element.inline_calculation] === "function") {
			try {
				price = window[element.inline_calculation](price);
				_this.add_debug_info("calculate", $element, "===\ninline calculation = " + price);
			}
			catch(e) {
				_this.add_debug_info("calculate", $element, "===\nInvalid inline calculation!");
			}
		}

		// force use external value (e.g. custom JS)
		if (this.get_element_object_value(element, "force_use_value_enabled", 0) == 1) {
			price = this.get_element_object_value(element, "force_use_value", 0);
		}

		// todo: move min/max set value?
		// min/max
		var element_min_value = this.get_element_object_value(element, "min", "");
		var element_max_value = this.get_element_object_value(element, "max", "");

		if (element_min_value != "") {
			element_min_value = parseFloat(element_min_value);
			price = Math.max(element_min_value, price);
		}
		if (element_max_value != "") {
			element_max_value = parseFloat(element_max_value);
			price = Math.min(element_max_value, price);
		}

		return price;
	};

	/**
		element calculations
	**/
	this.calculate_element_loop = function(element_id, price, read_only) {
		var element  = this.get_element_object(element_id);
		var $element = this.get_element_dom(element_id);
		var form_id  = element.form_id;

		var calc_enabled    = element.calculate_enabled;
		var overwrite_price = element.overwrite_price;
		var add_to_price    = element.add_to_price;

		var tmp_price;
		var addPrice = 0;

		if (calc_enabled == 0) {
			addPrice = 0;
		}
		// add calculated price to total price
		else if (calc_enabled == 1) {
			addPrice = this.calculate_element(form_id, element_id, price);
			// discount
			addPrice = this.calculate_discounts(element_id, addPrice, addPrice);

			if (add_to_price >= 1) {
				if (overwrite_price == 1) {
					price = addPrice;
				}
				else {
					price += addPrice;
				}
			}
			// for subtotal / set elements only (doesn't interfere with calculation but use the calculated price as text)
			else {
				if (overwrite_price == 1) {
					tmp_price = addPrice;
				}
			}
		}

		// write prices to input fields for certain elements but only when read_only flag isn't true (i.e. retrieve an element value without writing)
		if ((element.type == "subtotal" || element.type == "set" || element.type == "custom_calculation") && !read_only) {
			var $element_input = this.get_element_child_input_dom(element_id);

			if (calc_enabled == 0) {
				tmp_price = 0;
			}
			else if (calc_enabled == 1) {
				if (add_to_price == 1) {
					tmp_price = overwrite_price==1 ? price : addPrice;
				}
				else if (add_to_price == 2) {
					tmp_price = addPrice;
				}
				else {
					tmp_price = addPrice;
				}
			}

			$element_input.val(tmp_price);
			$element_input.data("converted", 0);

			this.set_element_object_value(element, "current_value_forced", tmp_price);
		}

		return price;
	};

	/**
		discount calculations
	**/
	this.calculate_discounts = function(element_id, price, custom_value) {
		var element        = this.get_element_object(element_id);
		var $element       = this.get_element_dom(element_id);
		var $element_input = this.get_element_child_input_dom(element_id);

		var overwrite_price = this.get_element_object_value(element, "overwrite_price", 0);

		// check if discounts exist
		if (!element || !element_id || !this.object_has_property(element, "discount")) return price;
		var discount = element.discount;

		// check if there should be discount actions
		if (discount) {
			var discount_range_min_values = discount.range_min;
			var discount_range_max_values = discount.range_max;
			var discount_operator_values  = discount.operator;
			var discount_value_values     = discount.values;
			var discount_value_type       = this.get_element_object_value(element, "discount_value_type", "calculated");
			var discount_value_apply      = this.get_element_object_value(element, "discount_value_apply", "calculated");

			var el_value = 0;
			var el_value_calculated;
			var factor = this.get_element_object_value(element, "factor_default", 1);
			if ((!factor || isNaN(factor)) && factor !== 0) factor = 1;
			factor = parseFloat(factor);
			// reset factor
			_this.set_element_object_value(element, "factor", factor);

			// get selected value from input fields
			if (element.type == "input" || element.type == "numbers" || element.type == "subtotal" || element.type == "hidden" || element.type == "extension") {
				el_value = this.normalize_value($element_input.val(), $element, null, $element_input.data("converted") == 0);
			}
			// get selected value from dropdowns
			else if (element.type == "dropdown") {
				el_value = parseFloat($element.find(":selected").data("value"));
			}
			// get selected value from radio
			else if (element.type == "radio") {
				el_value = parseFloat($element.find(":checked").data("value"));
			}
			// get selected values from checkboxes
			else if (element.type == "checkbox") {
				el_value = 0;
				$element.find(":checked").each(function(ct, ct_el) {
					el_value += parseFloat($(ct_el).data("value"));
				});
			}
			// get amount of days from date range
			else if (element.type == "daterange") {
				var tmp_target_value = [
					// from
					$element.find(".ezfc-element-daterange-from").datepicker("getDate"),
					// to
					$element.find(".ezfc-element-daterange-to").datepicker("getDate")
				];

				el_value = _this.jqueryui_date_diff(tmp_target_value[0], tmp_target_value[1], _this.get_element_object_value(element, "workdays_only", 0) == 1, element, discount_value_type);
				// get day prices
				el_value_calculated = _this.jqueryui_date_diff(tmp_target_value[0], tmp_target_value[1], _this.get_element_object_value(element, "workdays_only", 0) == 1, element);
			}

			// if calculated value wasn't calculated, use the default value
			if (!el_value_calculated && el_value_calculated != 0) {
				el_value_calculated = el_value;
			}
			// use calculated value
			if (discount_value_type == "calculated" && typeof custom_value !== "undefined") {
				el_value = custom_value;
			}
			var el_value_factor = el_value * factor;

			// use a different variable to check whether the condition is true
			var discount_compare_value = el_value;
			// discount table active ids
			var discount_active_ids = [];

			// go through all discounts
			$.each(discount_operator_values, function(id, operator) {
				if (discount_value_values[id].length < 1) return;
				
				if (!discount_range_min_values[id] && discount_range_min_values[id] !== 0) discount_range_min_values[id] = Number.NEGATIVE_INFINITY;
				if (!discount_range_max_values[id] && discount_range_max_values[id] !== 0) discount_range_max_values[id] = Number.POSITIVE_INFINITY;

				var discount_value_write_to_input;

				if (discount_compare_value >= parseFloat(discount_range_min_values[id]) && discount_compare_value <= parseFloat(discount_range_max_values[id])) {
					var disc_value_raw = discount_value_values[id];
					var disc_value = parseFloat(disc_value_raw);
					var discount_value_operator;

					discount_active_ids.push(id);

					// check which value to apply the discount on
					if (discount_value_apply == "calculated") {
						el_value = el_value_calculated;
					}

					switch (operator) {
						case "add":
							discount_value_operator = disc_value;
							discount_value_write_to_input = price + discount_value_operator;

							if (overwrite_price) {
								price = discount_value_write_to_input;
							}
							else {
								price += discount_value_write_to_input;
							}
						break;

						case "subtract":
							discount_value_operator = disc_value;
							discount_value_write_to_input = price - discount_value_operator;

							if (overwrite_price) {
								price = discount_value_write_to_input;
							}
							else {
								price -= discount_value_write_to_input;
							}
						break;

						case "percent_add":
							discount_value_operator = el_value_factor * (disc_value / 100);
							discount_value_write_to_input = el_value_factor + discount_value_operator;

							price = discount_value_write_to_input;
						break;

						case "percent_sub":
							discount_value_operator = el_value_factor * (disc_value / 100);
							discount_value_write_to_input = el_value_factor - discount_value_operator;

							price = discount_value_write_to_input;
						break;

						case "equals":
							discount_value_operator = disc_value;
							discount_value_write_to_input = discount_value_operator;

							price = discount_value_write_to_input;
						break;

						case "factor":
							discount_value_operator = el_value * disc_value;
							discount_value_write_to_input = discount_value_operator;

							_this.set_element_object_value(element, "factor", disc_value);
							price = discount_value_write_to_input;
						break;

						case "price_plus_factor":
							// input: 10|2 --> 10 + 2*x
							var discount_value_inputs = disc_value_raw.split("|");

							if (discount_value_inputs && discount_value_inputs.length >= 2) {
								discount_value_inputs = $.map(discount_value_inputs, parseFloat);
								var base_price  = discount_value_inputs[0];
								var base_factor = discount_value_inputs[1];

								discount_value_operator = base_price + base_factor * el_value;
								discount_value_write_to_input = discount_value_operator;

								_this.set_element_object_value(element, "factor", base_factor);
								price = discount_value_write_to_input;
							}
						break;
					}

					if (element.type == "subtotal" && !isNaN(discount_value_write_to_input)) {
						var $element_input = $($element).find("input");

						$element_input.val(discount_value_write_to_input);
						$element_input.data("converted", 0);
					}

					_this.add_debug_info("discount", $element, "discount = " + discount_value_operator + "\nprice after discount = " + price);
				}
			});

			_this.set_element_object_value(element, "__discount_table_active_ids", discount_active_ids);
		}

		return price;
	};

	// discount table update
	this.discount_table_update = function(element) {
		var active_ids  = _this.get_element_object_value(element, "__discount_table_active_ids", []);
		var table_cache = _this.get_element_object_value(element, "__discount_table_dom_cached", false);
		var highlight_class = "ezfc-element-discount-table-highlighted";
		var $table;

		// use table from cache
		if (table_cache) {
			$table = table_cache;
		}
		// cache table
		else {
			var $element = _this.get_element_dom(element.id);
			$table = $element.find(".ezfc-element-discount-table tbody");
			_this.set_element_object_value(element, "__discount_table_dom_cached", $table);
		}

		// remove highlighted classes
		$table.find(".ezfc-element-discount-table-highlighted").removeClass(highlight_class);

		// no active rows
		if (!active_ids) return;

		var $table_rows = $table.find("tr");
		
		for (var i in active_ids) {
			$table_rows.eq(active_ids[i]).addClass(highlight_class);
		}
	};

	/**
		set values for set elements
	**/
	this.calculate_set_elements = function(form, form_id, el_object, el_type, price) {
		var element          = this.get_element_object(el_object.data("id"));

		var set_operator     = this.get_element_object_value(element, "set_operator");
		var tmp_targets      = this.get_element_object_value(element, "set_elements");
		var allow_zero       = this.get_element_object_value(element, "set_allow_zero") == 1;
		var set_dom_selector = this.get_element_object_value(element, "set_dom_selector");
		var use_factor       = this.get_element_object_value(element, "set_use_factor", 1);

		var targets          = [];
		var value_to_write;

		// check if there should be conditional actions
		if (!tmp_targets && !set_dom_selector) return;

		if (set_dom_selector) {
			targets = $(set_dom_selector);
		}
		else {
			var tmp_targets_elements = tmp_targets;

			$.each(tmp_targets_elements, function(i, v) {
				var $target_object = $("#ezfc_element-" + v);
				if (!$target_object) return;

				targets.push($target_object);
			});
		}

		$.each(targets, function(i, tmp_element) {
			var $element = $(tmp_element);
			var target_element_object = _this.get_element_object($element.data("id"));
			var el_value = _this.get_value_from_element($element, null, false, use_factor == 0);

			// check for 0
			if (!allow_zero && el_value == 0) return;

			// first element
			if (i == 0) {
				value_to_write = el_value;
				return;
			}

			switch (set_operator) {
				case "min":
					if (el_value < value_to_write) value_to_write = el_value;
				break;

				case "max":
					if (el_value > value_to_write) value_to_write = el_value;
				break;

				case "avg":
				case "sum":
					value_to_write += el_value;
				break;

				case "dif":
					value_to_write -= el_value;
				break;

				case "prod":
					value_to_write *= el_value;
				break;

				case "quot":
					if (el_value != 0) value_to_write /= el_value;
				break;
			}
		});

		if (set_operator == "avg") {
			value_to_write = value_to_write / targets.length;
		}

		var value_to_write_formatted = this.normalize_value(value_to_write, el_object);

		element.current_value           = value_to_write;
		element.current_value_formatted = value_to_write_formatted;

		el_object.find("input").val(value_to_write_formatted);
	};

	// price calculation
	this.calculate_price = function(form, form_id) {
		form_id = form_id || $(form).data("id");
		var price = 0;

		// reset subtotals
		this.subtotals[form_id] = [];

		var element_trigger_list = this.trigger_list[form_id].length > 0 ? this.trigger_list[form_id] : _this.form_vars[form_id].form_elements_order;
		var return_early = true;

		// global conditionals
		if (_this.form_vars[form_id].hasOwnProperty("global_conditions")) {
			_this.calculate_conditionals(form, form_id, _this.form_vars[form_id].global_conditions, false, true);
		}

		// find all elements first
		for (var ie in element_trigger_list) {
			var element_id = element_trigger_list[ie];
			var element    = _this.get_element_object(element_id);
			var $element   = _this.get_element_dom(element_id);

			// get external value if present
			_this.calculate_get_external_values(form, form_id, $element, element.type);

			if (element.hasOwnProperty("conditional")) {
				// check conditionals
				_this.calculate_conditionals(form, form_id, element_id, element.type);
			}

			if (!element.hasOwnProperty("calculate") && element.type != "custom_calculation" && element.type != "matrix" && element.type != "set") continue;

			// some element is being calculated, so don't return early
			return_early = false;

			// check if element is hidden
			if (!_this.element_is_visible(element_id) && element.calculate_when_hidden == 0 && element.type != "hidden" && element.type != "custom_calculation") continue;

			// set elements
			_this.calculate_set_elements(form, form_id, $element, element.type);
		}

		// return price because nothing else should happen
		if (return_early) return this.price_old_global[form_id];

		// recalculate price (lite)
		var repeatable_price_first_element_id = 0;
		var repeatable_price_first_element_parent_id = 0;
		var repeatable_price_holder = 0;
		for (var i in _this.form_vars[form_id].form_elements_order) {
			var element_id = _this.form_vars[form_id].form_elements_order[i];
			var element    = _this.get_element_object(element_id);
			var $element   = _this.get_element_dom(element_id);

			if (!element.hasOwnProperty("calculate") && element.type !== "custom_calculation" && element.type != "matrix" && element.type != "set" && element.type != "repeatable_form") continue;

			// check if first element of repeatable group -> reset and keep price
			if (this.has_repeatable_parent_id(element_id) && !repeatable_price_first_element_id) {
				repeatable_price_first_element_id = element_id;
				repeatable_price_first_element_parent_id = this.get_repeatable_parent_id(element_id);
				repeatable_price_holder = price;
				price = 0;
			}
			// check if element is first of repeated elements -> reset sub loop
			else if (this.has_repeatable_parent_id(element_id) && this.get_repeatable_element_id(element_id) == repeatable_price_first_element_id) {
				repeatable_price_holder += price;
				price = 0;
			}
			// restore and add sub loop price
			else if (element.type == "repeatable_form" && element_id == repeatable_price_first_element_parent_id) {
				price = repeatable_price_holder + price;
				repeatable_price_first_element_id = 0;
				repeatable_price_first_element_parent_id = 0;
				repeatable_price_holder = 0;

				continue;
			}

			// process calculations
			var element_loop_price   = _this.calculate_element_loop(element_id, price);
			var element_single_price = _this.calculate_element_loop(element_id, 0, true);

			// check conditionals again
			_this.calculate_conditionals(form, form_id, element_id, element.type);

			// add to element js vars
			_this.set_element_object_value(element_id, "current_value", element_single_price);

			// if element adds value to global price and can calculate due to visibility settings, set the current price
			if (_this.get_element_object_value(element, "calculate_enabled", 1) == 1 && _this.get_element_object_value(element, "add_to_price", 1) > 0 && this.element_can_calculate_visibility(element_id)) {
				price = element_loop_price;
			}

			// if add to price is disabled, get the calculated price via input
			if (_this.get_element_object_value(element, "add_to_price", 1) == 0) {
				var current_value = _this.get_value_from_element($element, null, true);
				_this.set_element_object_value(element, "current_value", current_value);
			}

			// add to subtotals
			if (_this.get_element_object_value(element, "calculate_enabled", 1) == 1) {
				_this.subtotals[form_id].push({
					el_id: element_id,
					price: price
				});
			}
		}

		return price;
	};

	this.set_price = function(form, price_old, force_price) {
		var form_id = $(form).data("id");

		// calculate price
		if (force_price) {
			price = force_price;
		}
		else if (!price_old || price_old !== 0) {
			price = this.calculate_price($(form));
		}

		// update subtotal elements
		this.set_subtotal_values($(form));
		// update table order elements
		this.update_table_order_all(form_id);

		// show price after request
		if (this.form_vars[form_id].price_show_request == 1 && this.form_vars[form_id].price_requested == 0) {
			this.price_request_toggle(form_id, false);
			return;
		}

		if (typeof this.price_old_global[form_id] === "undefined" || isNaN(this.price_old_global[form_id])) this.price_old_global[form_id] = 0;

		$form_price_elements = $(document).find(".ezfc-price-value[data-id='" + form_id + "']");

		if (this.price_old_global[form_id] == price) {
			$form_price_elements.text(this.format_price(form_id, price));
			return;
		}

		if (this.form_vars[form_id].counter_duration != 0) {
			$form_price_elements.countTo({
				from: _this.price_old_global[form_id],
				to: price,
				speed: this.form_vars[form_id].counter_duration,
				refreshInterval: this.form_vars[form_id].counter_interval,
				formatter: function (value, options) {
					var price_formatted = _this.format_price(form_id, value);
					var price_numeral   = numeral(price_formatted).value();

					if (isNaN(price_numeral) || !price_numeral) price_formatted = 0;

					return price_formatted;
				},
				onComplete: function(value) {
					$form_price_elements.text(_this.format_price(form_id, price));
				}
			});
		}
		else {
			$form_price_elements.text(this.format_price(form_id, price));
		}

		this.price_old_global[form_id] = price;
	};

	/**
	**/
	this.conditional_check_multiple_options = function(needles, haystack) {
		var do_action_index = false;

		// needle is a string -> convert to array
		if (typeof needles === "string" || typeof haystack === "number") {
			needles = String(needles).split("|");
			needles = $.map(needles, $.trim);
		}
		// haystack is a string -> convert to array
		if (typeof haystack === "string" || typeof haystack === "number") {
			haystack = String(haystack).split("|");
			haystack = $.map(haystack, $.trim);
		}

		for (var i in needles) {
			for (var t in haystack) {
				if (haystack[t] == needles[i]) {
					do_action_index = true;
				}
			}
		}

		return do_action_index;
	};

	/**
		formats a value with element settings
	**/
	this.format_value_element = function(element_id, price) {
		var element = this.get_element_object(element_id);

		return this.format_price(element.form_id, price, null, element.price_format, element.is_currency, true);
	};

	this.format_price = function(form_id, price, currency, custom_price_format, format_with_currency, disable_normalize, element) {
		var form_price_format = this.defaultFormat;
		var form_currency     = currency || this.form_vars[form_id].currency;

		// use price format from form settings
		if (this.form_vars[form_id].price_format && this.form_vars[form_id].price_format.length) {
			form_price_format = this.form_vars[form_id].price_format;
		}

		// if defined, use custom price format
		if (typeof custom_price_format !== "undefined" && custom_price_format !== null) {
			form_price_format = custom_price_format;
		}

		if (isNaN(price) || price === null) price = 0;

		// use parsed value or parse with numeraljs
		var price_value = disable_normalize ? numeral().set(price) : numeral(price);
		var price_format_string = !this.is_empty(form_price_format) ? form_price_format : this.defaultFormat;
		var price_formatted = price_value.format(price_format_string);

		// if the value can't be formatted, use its raw value
		if (price_formatted === "NaN") price_formatted = price;

		// replace trailing zeros: 10,00 -> 10,-
		if (ezfc_vars.price_format_replace_trailing_zeros.enabled == 1) {
			// price sometimes looks like 8.0000000000017, so take the first decimal numbers and check if it's an integer
			if (parseFloat(price).toFixed(6) % 1 === 0) {
				price_formatted += ezfc_vars.price_format_dec_point + ezfc_vars.price_format_replace_trailing_zeros.text;
			}
		}

		// add currency symbol
		if (format_with_currency == 1 && _this.form_vars[form_id].format_currency_numbers_elements == 1) {
			if (this.form_vars[form_id].currency_position == 0) {
				price_formatted = form_currency + price_formatted;
			}
			else {
				price_formatted = price_formatted + form_currency;
			}
		}

		return price_formatted;
	};

	// price request toggler
	this.price_request_toggle = function(form_id, enable, price) {
		var form = $(".ezfc-form[data-id='" + form_id + "']");

		// enable submit
		if (enable) {
			this.price_old_global[form_id] = 0;
			this.form_vars[form_id].price_requested = 1;
			// calculate form price so element values show the correct price
			this.calculate_price(form);
			// set request price
			this.set_price(form, null, price);
			// set submit button text
			this.set_submit_text(form);
		}
		else {
			this.form_vars[form_id].price_requested = 0;

			$(form).find(".ezfc-price-value").text(this.form_vars[form_id].price_show_request_before);
			$(form).find(".ezfc-submit").val(this.form_vars[form_id].submit_text.request);
		}
	};

	/**
		convert real value to formatted value
	**/
	this.set_subtotal_values = function(form, form_id) {
		form_id = form_id || $(form).data("id");

		var element_list                = ["subtotal", "set", "numbers", "custom_calculation"];
		var force_format_input_elements = ["subtotal", "set"];

		$.each(_this.form_vars[form_id].form_elements_order, function(i, element_id) {
			var element  = _this.get_element_object(element_id);
			var $element = _this.get_element_dom(element_id);

			if ($.inArray(element.type, element_list) === -1) return;

			var precision = _this.get_element_object_value(element, "precision", 2);
			var price_format = element.price_format;

			// use different element for custom_js elements
			if (element.type == "custom_calculation") {
				$element_input = $element.find(".ezfc-element-custom-calculation-input");
			}
			else {
				$element_input = $element.find(".ezfc-element-" + element.type);
			}

			var value_pre = element.current_value;
			// get forced value (conditionally set)
			if (_this.object_has_property(element, "current_value_forced")) {
				value_pre = element.current_value_forced;
			}

			// check if element is repeated
			if (_this.is_repeatable_element(element_id) && element.type == "subtotal") {
				// get element directly form input
				value_pre = $(_this.get_element_child_input_dom(element_id)).val();
			}

			// retrieve and normalize
			var value = _this.normalize_value(value_pre, $element_input, precision, $element_input.data("converted") == 0);

			// check for min/max
			if ($element_input.data("min") != "") {
				var min_value = parseFloat($element_input.data("min"));
				if (min_value > value) value = min_value;
			}
			if ($element_input.data("max") != "") {
				var max_value = parseFloat($element_input.data("max"));
				if (max_value < value) value = max_value;
			}

			var text = "", text_currency = "";
			// text before/after
			var text_before = _this.get_element_object_value(element, "text_before", "");
			var text_after  = _this.get_element_object_value(element, "text_after", "");

			// don't format zero values
			if (_this.form_vars[form_id].format_number_show_zero == 0 && (value == "" || value == 0)) {
				value = "";
			}
			else {
				text = _this.format_price(element.form_id, value, null, price_format, false, $element_input.data("converted") == 0);
				text_currency = _this.format_price(element.form_id, value, null, price_format, element.is_currency, $element_input.data("converted") == 0);
				
				text_currency = text_before + text_currency + text_after;
			}

			// force-format for some elements only
			if ($.inArray(element.type, force_format_input_elements) !== -1) {
				// set formatted value
				$element_input.val(text_currency);
				// set converted flag
				$element_input.data("converted", 1);
			}

			$element.find(".ezfc-text").text(text);
		});
	};

	this.scroll = function() {
		$(".ezfc-fixed").each(function() {
			var form_id = $(this).data("id");

			// form not yet initialised
			if (!_this.form_vars[form_id]) return;

			var active_scroll_class = "ezfc-fixed-scrolling";
			var offset              = $(this).offset();
			var form                = $(".ezfc-form[data-id='" + form_id + "']");
			var form_height         = form.outerHeight();
			var form_offset         = form.offset();
			var window_top          = $(window).scrollTop();
			var price_position_top  = parseFloat(_this.form_vars[form_id].price_position_scroll_top);

			var diff = form_offset.top - window_top - price_position_top;
			if (diff < 0 && diff > -form_height) {
				$(this).offset({ top: window_top + price_position_top });
				$(this).addClass(active_scroll_class);
			}
			else if (diff > 0 && offset.top > form_offset.top) {
				$(this).offset({ top: form_offset.top });
				$(this).removeClass(active_scroll_class);
			}
			// stay in form boundaries
			else if (offset.top < form_offset.top || offset.top > form_offset.top + form_height) {
				$(this).offset({ top: form_offset.top });
			}
		});
	};

	// reset disabled fields and restore initial values (since they may have changed due to conditional logic). also, set the relevant submit button text
	this.reset_disabled_fields = function(form, error) {
		var $form_wrapper = $(form).closest(".ezfc-wrapper");

		$(form).find(".ezfc-custom-hidden").each(function() {
			$.each($(this).find("input, :selected"), function(i, v) {
				$(this).val($(this).data("index")).removeAttr("disabled");

				if ($(this).is(":checked")) {
					$(this).prop("checked", false);
				}
			});
		});

		// also reset payment fields
		$form_wrapper.find(".ezfc-payment-submit, .ezfc-payment-cancel").prop("disabled", false);

		this.set_submit_text(form, error);
	};

	// reset the whole form
	this.reset_form = function($form) {
		// re-init form
		this.init_form($form);

		// reset elements to initial values
		this.reset_elements($form);

		this.set_step($form, 0, 0);
		this.form_change($form);
	};

	this.reset_elements = function($wrapper) {
		// reset values
		$wrapper.find(".ezfc-custom-element").each(function() {
			var element_id = $(this).data("id");
			var element    = _this.get_element_object(element_id);

			// skip groups
			if (element.type == "group") return;

			// don't reset field
			if (_this.get_element_object_value(element, "keep_value_after_reset", 0) == 1) return;

			var el_type = $(this).data("element");
			var initvalue = $(this).find("[data-initvalue]").data("initvalue");

			switch (el_type) {
				case "checkbox":
					$(this).find("input").each(function() {
						initvalue = $(this).data("initvalue");

						if (initvalue == 1)
							$(this).prop("checked", true);
						else
							$(this).removeAttr("checked");
					});
				break;

				case "dropdown":
					$(this).find("option").removeAttr("selected");
					$(this).find("option[data-index='" + initvalue + "']").prop("selected", true);
				break;

				case "numbers":
					$(this).find("input").val(initvalue);
					var $sliders = $(this).find(".ezfc-slider-element");
					if ($sliders.length) $sliders.slider({ value: initvalue });
				break;

				case "radio":
					$(this).find("input").removeAttr("checked");
					$(this).find("input[data-initvalue]").prop("checked", true);
				break;

				case "textfield":
					$(this).find("textarea").val(initvalue);
				break;

				default:
					$(this).find("input").val(initvalue);
				break;
			}
		});

		$wrapper.find(".ezfc-selected").removeClass("ezfc-selected");
	};

	this.set_step = function(form, new_step, verify) {
		var current_step  = parseInt(form.find(".ezfc-step-active").data("step"));
		var $step_wrapper = form.find(".ezfc-step[data-step='" + current_step + "']");
		var form_id       = form.data("id");

		this.step_is_changing = false;

		if (current_step == new_step) return;

		// check ajax
		if (verify == 1 && this.form_vars[form_id].verify_steps == 1) {
			var submit_icon = form.find(".ezfc-step-submit-icon");
			submit_icon.fadeIn();

			this.form_submit(form, new_step - 1);

			$(".ezfc-has-hidden-placeholder").val("").removeClass("ezfc-has-hidden-placeholder");
			return;
		}

		// step indicator
		var step_indicator_start = parseInt(this.form_vars[form_id].step_indicator_start) - 1;
		var step_fade_speed = parseInt(this.form_vars[form_id].step_speed);
		if (isNaN(step_fade_speed)) step_fade_speed = 200;

		$step_wrapper.fadeOut(step_fade_speed, function() {
			var step_wrapper_next = form.find(".ezfc-step[data-step='" + new_step + "']");
			
			step_wrapper_next.fadeIn(step_fade_speed).addClass("ezfc-step-active");
			$(this).removeClass("ezfc-step-active");

			// maybe show step indicator
			if (new_step >= step_indicator_start) {
				form.find(".ezfc-step-indicator").fadeIn();
			}
			else {
				form.find(".ezfc-step-indicator").hide();
			}

			_this.scroll_to(step_wrapper_next);
		});

		form.find(".ezfc-step-indicator-item").each(function() {
			var step_dom = parseInt($(this).data("step"));
			$(this).removeClass("ezfc-step-indicator-item-active");
			
			if (step_dom <= new_step) {
				$(this).addClass("ezfc-step-indicator-item-active");
			}
		});

		// reset succeeding steps
		if (new_step < current_step && _this.form_vars[form_id].step_reset_succeeding == 1) {
			for (var s = new_step + 1; s < _this.form_vars[form_id].step_count; s++) {
				var $step_wrapper_reset = $(".ezfc-step[data-step='" + s  +"']");

				this.reset_elements($step_wrapper_reset);
			}
		}

		// set current step
		_this.current_steps[form_id] = new_step;

		_this.form_change(form);

		return false;
	};

	this.set_step_action = function(form_id, action, target_id) {
		if (this.step_is_changing) return;

		var $form = $("#ezfc-form-" + form_id);

		var current_step = parseInt($form.find(".ezfc-step-active").data("step"));
		var next_step = 0;

		switch (action) {
			case "step_prev":
				if (current_step == 0) return;
				next_step = current_step - 1;
			break;

			case "step_next":
				var step_length = $form.find(".ezfc-step-start").length;
				if (current_step == step_length - 1) return;

				next_step = current_step + 1;
			break;

			case "step_goto":
				target_id = parseInt(target_id);
				if (isNaN(target_id)) return;

				var step_goto = $form.find(".ezfc-step-start[data-id='" + target_id + "']");
				if (step_goto.length < 1) return;

				next_step = parseInt(step_goto.data("step"));
			break;
		}

		this.step_is_changing = true;

		setTimeout(function() {
			_this.set_step($form, next_step, 1);
		}, 350);
	};

	this.scroll_to = function(element, custom_offset) {
		var element_offset = $(element).offset();

		if (typeof element_offset === "undefined" || ezfc_vars.auto_scroll_steps == 0) return;

		// take global offset first
		var offset_add = parseFloat(custom_offset) || 0;

		if (!this.is_empty(ezfc_vars.scroll_steps_offset)) {
			offset_add = parseFloat(ezfc_vars.scroll_steps_offset);
		}

		var offset_scroll = element_offset.top + offset_add;

		$("html, body").animate({ scrollTop: offset_scroll });
	};

	/**
		get cached dom element
	**/
	this.get_element_dom = function(element_id) {
		// return uncached dom element
		if (this.is_repeatable_element(element_id)) return $("#ezfc_element-" + element_id);

		if (typeof this.$form_elements[element_id] === "undefined") return false;

		return this.$form_elements[element_id].$element;
	};
	/**
		get cached dom child input element
	**/
	this.get_element_child_input_dom = function(element_id) {
		// return uncached dom element
		if (this.is_repeatable_element(element_id)) return $("#ezfc_element-" + element_id).find(":input");

		if (typeof this.$form_elements[element_id] === "undefined") return false;

		return this.$form_elements[element_id].$element_input;
	};

	/**
		get element object option
	**/
	this.get_object_value = function(obj, option, default_value, array_index) {
		if (typeof obj !== "object") return default_value;

		if (!obj || !this.object_has_property(obj, option)) return default_value;

		// return array index item
		if (typeof obj[option] === "object" && typeof array_index !== "undefined" && typeof obj[option][array_index] !== "undefined") return obj[option][array_index];

		return obj[option];
	};

	/**
		get element from object vars
	**/
	this.get_element_object = function(element_id) {
		// no element found
		if (typeof this.$form_elements[element_id] === "undefined") return false;

		return this.$form_elements[element_id].element;
	};
	/**
		get element object option
	**/
	this.get_element_object_value = function(element, option, default_value) {
		if (typeof element !== "object") element = this.get_element_object(element);

		if (!element || !this.object_has_property(element, option)) return default_value;

		return element[option];
	};

	this.get_repeatable_element_id = function(element_id) {
		if (!this.is_repeatable_element(element_id)) return element_id;

		if (String(element_id).indexOf("-") === -1) return element_id;

		return String(element_id).split("-")[0];
	};

	this.get_repeatable_element_counter = function(element_id) {
		if (!this.is_repeatable_element(element_id)) return element_id;

		if (String(element_id).indexOf("-") === -1) return 0;

		return String(element_id).split("-")[1];
	};
	this.get_repeatable_element_counter_from_dom_id = function(element_dom_id) {
		var id_parts = String(element_dom_id).split("-");
		if (id_parts.length < 3) return false;

		return id_parts[2];
	};

	// checks if an element is repeated
	this.is_repeatable_element = function(element_id) {
		if (typeof element_id !== "string" && typeof element_id !== "number") return false;

		return String(element_id).indexOf("-") >= 0 || this.has_repeatable_parent_id(element_id);
	};

	this.get_repeatable_parent_id = function(element_id) {
		// check manually to prevent max call size
		if (String(element_id).indexOf("-") >= 0) {
			element_id = String(element_id).split("-")[0];
		}

		return this.get_element_object_value(this.get_element_object(element_id), "__parent_id", 0);
	};

	this.has_repeatable_parent_id = function(element_id) {
		return this.get_repeatable_parent_id(element_id) != 0;
	};

	/**
		set element object var
	**/
	this.set_element_object_value = function(element, option, value) {
		// ID or special value
		if (typeof element !== "object") {
			if (element == "price" || element == "submit_button") return;

			element = this.get_element_object(element);
		}

		if (!element) return false;

		element[option] = value;
	};
	/**
		set element object var in list
	**/
	this.set_element_list_object_value = function(list, option, value) {
		for (var i in list) {
			_this.set_element_object_value(list[i], option, value);
		}
	};

	/**
		get top parent group (recursively)
	**/
	this.get_top_parent_group_id = function(group_id) {
		var element = this.get_element_object(group_id);
		if (!element) return false;

		// check parent group
		if (element.group_id != 0) return this.get_top_parent_group(element.group_id);

		return element.id;
	};
	/**
		find parent group id
	**/
	this.has_parent_group_id = function(element_id, find_group_id) {
		var element = this.get_element_object(element_id);

		// no parent group found
		if (!element.group_id || element.group_id == 0) return false;

		// self
		if (element_id == find_group_id) return false;

		// group id found
		if (element.group_id == find_group_id) return true;

		return this.has_parent_group_id(element.group_id, find_group_id);
	};

	/**
		get group child elements
	**/
	this.get_group_children = function(group_id) {
		var group_element = this.get_element_object(group_id);
		if (!group_element) return [];

		var group_children_list = [];
		for (var i in _this.form_vars[group_element.form_id].form_elements_order) {
			var element_id = _this.form_vars[group_element.form_id].form_elements_order[i];
			var element    = _this.get_element_object(element_id);

			// exclude self
			if (element_id == group_id) continue;

			// element is in group
			if (_this.has_parent_group_id(element_id, group_id)) {
				group_children_list.push(element_id);
			}
		}

		return group_children_list;
	};

	this.get_value_from_element = function($el_object, element_id, is_text, ignore_factor, options) {
		var default_options = {
			return_array: false,
			return_value: "value"
		};
		options = $.extend({}, default_options, options);

		// get element DOM object
		if (!$el_object && element_id) {
			if (typeof this.$form_elements[element_id] !== "undefined") {
				$el_object = this.$form_elements[element_id].$element;
			}
			else {
				$el_object = this.get_element_dom(element_id);
			}
		}

		if (!$el_object || !$el_object.length) {
			this.debug_message("Unable to find element #" + element_id, 2);
			return 0;
		}

		if (!element_id) element_id = $el_object.data("id");

		var element = this.get_element_object(element_id);
		if (!element) return 0;

		var $input_element    = this.get_element_child_input_dom(element_id);
		var el_type           = element.type;
		var factor            = this.get_element_object_value(element_id, "factor", 1);
		var value_raw         = $input_element.val();
		var disable_normalize = $input_element.data("converted") == 0;
		//var value_is_pct      = value_raw ? value_raw.indexOf("%") >= 0 : 0;
		var value_is_pct      = false;
		var value             = this.normalize_value(value_raw, $el_object, null, disable_normalize);
		var value_pct         = value / 100;

		// check if visible
		if (element.calculate_when_hidden == 0 && !this.element_is_visible(element_id) && el_type != "hidden" && el_type != "custom_calculation") return 0;

		// default values
		if (!value || isNaN(value)) value = 0;

		// set addprice to value first
		var return_value = is_text ? value : parseFloat(value);

		// return selected elements (checkboxes)
		if (options.return_value == "count") return_value = 0;

		// basic calculations
		switch (el_type) {
			case "subtotal":
			case "numbers":
			case "hidden":
				if ((!factor || isNaN(factor)) && factor !== 0) factor = 1;

				if (is_text) {
					return_value = value_raw;
				}
				else {
					return_value = ignore_factor ? value : value * factor;
				}
			break;

			case "dropdown":
			case "radio":
			case "checkbox":
				// return array of selected values from index or actual value
				var read_value = options.return_value;
				var return_calculated_value = !is_text && $.inArray(read_value, ["count", "name", "option_id"]) === -1;

				$el_object.find(":selected, :checked").each(function() {
					// add up values by default
					if (return_calculated_value) {
						return_value += parseFloat($(this).data("value"));
					}
					// return array of checked / selected values
					else {
						if (typeof return_value !== "object" && read_value != "count") return_value = [];

						// check if name should be retrieved
						if (read_value == "name") {
							var index = $(this).data("index");
							return_value.push(element.options[index].text);
						}
						// option ID
						else if (read_value == "option_id") {
							return_value.push($(this).data("optionid"));
						}
						// count
						else if (read_value == "count") {
							return_value++;
						}
						// retrieve index or value
						else {
							return_value.push($(this).data(read_value));
						}
					}
				});
			break;

			case "datepicker":
				var datepicker_format = $.trim(_this.form_vars[element.form_id].datepicker_format);
				if (datepicker_format == "") datepicker_format = "dd/mm/yy";

				var date_raw = $el_object.find(".ezfc-element-datepicker").datepicker("getDate");
				return_value = $.datepicker.formatDate(datepicker_format, date_raw);
				is_text = true;
			break;

			case "daterange":
				var datepicker_format = $.trim(_this.form_vars[element.form_id].datepicker_format);
				if (datepicker_format == "") datepicker_format = "dd/mm/yy";

				var tmp_target_value = [
					// from
					$el_object.find(".ezfc-element-daterange-from").datepicker("getDate"),
					// to
					$el_object.find(".ezfc-element-daterange-to").datepicker("getDate")
				];
				// format dates
				var date_values = [
					$.datepicker.formatDate(datepicker_format, tmp_target_value[0]),
					$.datepicker.formatDate(datepicker_format, tmp_target_value[1]),
				];

				// return dates as array
				if (is_text) {
					return_value = date_values;
				}
				// return date difference in days
				else {
					var $daterange_container = $el_object.find(".ezfc-element-daterange-container");
					var days = _this.jqueryui_date_diff(tmp_target_value[0], tmp_target_value[1], this.get_element_object_value(element, "workdays_only", 0) == 1, element);

					return_value = ignore_factor ? days : days * factor;
				}
			break;

			// custom calculation function
			case "custom_calculation":
				return_value = $el_object.find(".ezfc-element-custom-calculation-input").val();

				if (!is_text) {
					return_value = parseFloat(return_value);
				}
			break;

			case "starrating":
				return_value = parseFloat($el_object.find(":checked").val());
				if (isNaN(return_value)) return_value = 0;
			break;

			case "table_order":
				var tmp_total = 0;

				$.each(element.table_order, function(i, row) {
					var $input_id = $el_object.find(".ezfc-element-table_order-row[data-row='" + i + "'] .ezfc-element-table_order-quantity-input");

					var qty = parseFloat($input_id.val());
					var row_total = parseFloat(row.value) * qty;

					// increase qty
					if (options.return_value == "count") tmp_total += qty;
					// increase price
					else tmp_total += row_total;
				});

				return_value = tmp_total;
			break;

			case "set":
				return_value = element.current_value;
			break;

			case "input":
			case "textfield":
				if (options.return_value == "letter_count") {
					return_value = String(value_raw).replace(/\s+/g, "").length;
				}
				else if (options.return_value == "letter_count_with_space") {
					return_value = String(value_raw).replace(/\r?\n|\r/g, "").length;
				}
				else {
					return_value = value_raw;
				}
			break;

			case "repeatable_form":
				var calc_value = 0;
				var repeated_element_ids = this.get_element_object_value(element, "__repeated_ids", []);
				var repeated_elements_count = this.get_element_object_value(element, "__repeated_elements_count", 0);

				var repeatable_price_first_element_id = repeated_element_ids[0];
				var repeatable_price_holder = 0;
				var element_counter_index  = 0;

				// loop through each repeated element
				for (var i in repeated_element_ids) {
					var loop_element_id = repeated_element_ids[i];
					var loop_element    = this.get_element_object(repeated_element_ids[i]);
					var loop_value      = this.get_calculated_element_value(loop_element.form_id, loop_element_id);

					if (loop_element.overwrite_price == 1) {
						calc_value = loop_value;
					}
					else {
						calc_value += loop_value;
					}

					if (element_counter_index > 0 && element_counter_index % (repeated_elements_count - 1) == 0) {
						repeatable_price_holder += calc_value;
						calc_value = 0;

						// reset counter index
						element_counter_index = 0;
					}
					else {
						element_counter_index++;
					}
				}

				// restore price
				calc_value = repeatable_price_holder + calc_value;

				return_value = calc_value;
			break;

			// extensions
			case "discount_codes":
				return_value = _this.get_element_object_value(element, "discount_value", 0);
			break;

			case "address_distance":
				return_value = _this.get_element_object_value(element, "distance_value", 0);
			break;

			// no calculation elements
			case "fileupload":
			case "group":
			case "heading":
			case "hr":
			case "html":
			case "image": // todo?
			case "placeholder":
			case "spacer":
			case "stepstart":
			case "stepend":
				return_value = 0;
			break;

			// default value
			default:
				return_value = value_raw;
			break;
		}

		// percent calculation
		if (value_is_pct) {
			return_value = value_pct;
		}

		if (is_text) {
			if (read_value == "name") {
				var parser = new DOMParser();
				var dom = parser.parseFromString(return_value, "text/html");

				if (dom.body && dom.body.textContent) {
					return_value = dom.body.textContent;
				}
			}
		}
		else {
			if (isNaN(return_value)) return_value = 0;
			
			return !return_value ? 0 : parseFloat(return_value);
		}

		return return_value;
	};

	/**
		checkbox was selected / deselected
	**/
	this.checkbox_change = function($element) {
		// check for min / max selectable
		var max_selectable = parseInt($element.attr("data-max_selectable"));

		if (!max_selectable) {
			$element.find("input:not(:checked):not(.force-disabled)").removeAttr("disabled");
		}

		if (max_selectable && max_selectable >= 0) {
			var selected = $element.find(":checked").length;

			if (selected >= max_selectable) {
				$element.find("input:not(:checked)").attr("disabled", "disabled");
			}
			else {
				$element.find("input:not(:checked):not(.force-disabled)").removeAttr("disabled");	
			}
		}
	};

	/**
		checkbox change state
	**/
	this.checkbox_change_state = function($checkbox, state, disable_change_trigger, change_image_only) {
		// exit when checkbox is disabled
		if ($checkbox.attr("disabled")) return false;

		// toggle if state wasn't defined
		if (typeof state === "undefined" || state === null) {
			state = !$checkbox.is(":checked");
		}

		var $parent_container = $checkbox.closest(".ezfc-element-single-option-container");
		var has_image = $parent_container.hasClass("ezfc-element-option-has-image");
		// image checkboxes
		if (has_image) {
			var $image = $checkbox.siblings(".ezfc-element-option-image");

			// uncheck it -> remove selected class
			if (!state) {
				$image.removeClass("ezfc-selected");
			}
			// check it
			else {
				$image.addClass("ezfc-selected");
			}
		}

		// wrapper class
		if (!state) {
			$parent_container.removeClass("ezfc-option-wrapper-selected");
		}
		else {
			$parent_container.addClass("ezfc-option-wrapper-selected");
		}

		// labels handle the checked prop themselves, so sometimes we only want to change image states
		if (!change_image_only) {
			$checkbox.prop("checked", state);
		}

		if (!disable_change_trigger) {
			$checkbox.trigger("change");
		}
	};

	/**
		radio change state
	**/
	this.radio_change_state = function($radio, state, disable_change_trigger) {
		var form_id = $radio.closest(".ezfc-form").data("id");

		// exit when checkbox is disabled
		if ($radio.attr("disabled")) return false;

		// toggle if state wasn't defined
		if (state === undefined) {
			state = !$radio.is(":checked");
		}

		var $parent_container = $radio.closest(".ezfc-element-radio-container");
		var $parent_wrapper   = $parent_container.closest(".ezfc-element-option-container");

		// check for connected elements
		//var element = this.get_element_object($parent_container.closest(".ezfc-element").data("id"));
		//var connected_elements = this.get_element_object_value(element, "options_connected", []);
		//console.log(connected_elements);
		// --> conditional actions?

		// remove selected class from images
		$parent_wrapper.find(".ezfc-selected").removeClass("ezfc-selected");
		// "uncheck"
		$parent_wrapper.find(".ezfc-element-radio-input").removeAttr("checked");

		// add wrapper class
		if (state) {
			$parent_container.siblings(".ezfc-option-wrapper-selected").removeClass("ezfc-option-wrapper-selected");
			$parent_container.addClass("ezfc-option-wrapper-selected");
		}

		var has_image = $parent_container.hasClass("ezfc-element-option-has-image");
		// image checkboxes
		if (has_image) {
			var $image = $radio.siblings(".ezfc-element-option-image");
			
			if (state) {
				// check radio input
				$radio.attr("checked", "checked");
				$radio.prop("checked", true);
				// remove selected class
				$parent_wrapper.find(".ezfc-selected").removeClass("ezfc-selected");
				// add selected class
				$image.addClass("ezfc-selected");
			}
		}
		// simple radio
		else if (state) {
			$radio.attr("checked", "checked");
			$radio.prop("checked", true);
		}

		if (!disable_change_trigger) {
			$radio.trigger("change");
		}

		// auto progress steps
		if (this.form_vars[form_id] && this.form_vars[form_id].step_auto_progress == 1 && !$parent_wrapper.hasClass("disable-auto-progress") && state) {
			this.set_step_action(form_id, "step_next");
		}
	};

	/**
		live summary
	**/
	this.live_summary_update = function(form_id) {
		if (this.form_vars[form_id].live_summary_enabled == 0) return;

		var _this = this;
		var out = "";

		$.each(ezfc_vars.element_vars[form_id], function(i, element) {
			if (typeof element["current_value"] === "undefined" || element.show_in_live_summary == 0 || !_this.element_is_visible(element.id)) return;

			var current_value = $.trim(element.current_value);

			if (typeof element["options"] !== "undefined") {
				current_value = _this.get_value_from_element(null, element.id, true, null, { return_value: "name" });

				if (current_value == 0) return;
			}
			else if (typeof element["current_value_formatted"] !== "undefined") {
				current_value = element.current_value_formatted;
			}

			if (current_value == "" || current_value == 0) return;

			if (typeof current_value === "object") {
				current_value = "<ul class='ezfc-live-summary-list'><li class='ezfc-live-summary-list-item'>" + current_value.join("</li><li class='ezfc-live-summary-list-item'>") + "</li></ul>";
			}

			// call hook so values can be modified individually
			var hook_values = {
				current_value: current_value,
				element: element,
				form_id: form_id
			};
			var hook_return = _this.call_hook("ezfc_live_summary_item", hook_values);
			current_value = hook_return.current_value;

			out += "<div class='ezfc-live-summary-item'><div class='ezfc-live-summary-item-name'>" + element.label + "<span class='ezfc-live-summary-item-separator'></span></div><div class='ezfc-live-summary-item-value'>" + current_value + "</div></div>";
		});

		$("#ezfc-live-summary-" + form_id).find(".ezfc-live-summary-content").html(out);
	};

	/**
		input with format listener changed
	**/
	this.input_format_listener_change = function($element) {
		var $el_wrapper = $element.parent().closest(".ezfc-element");
		var element_id  = $el_wrapper.data("id");
		var element     = this.get_element_object(element_id);
		if (!element) return;

		var form_id = element.form_id;

		var text_before  = this.get_element_object_value(element, "text_before", "");
		var text_after   = this.get_element_object_value(element, "text_after", "");
		var price_format = this.get_element_object_value(element, "price_format", null);

		$element.on("focus click", function() {
			var regex_text_before = _this.escape_regex(text_before);
			var regex_text_after  = _this.escape_regex(text_after);

			// replace prefix
			if (text_before != "") {
				this.value = this.value.replace(new RegExp("^" + regex_text_before, ""), "");
			}
			// replace suffix
			if (text_after != "") {
				this.value = this.value.replace(new RegExp(regex_text_after + "$", ""), "");
			}

			// convert to numbers only (with decimal point)
			if (element.is_number == 1) {
				this.value = this.value.replace(/[^0-9\.,-]/g, "");
				this.value = this.value.replace(ezfc_vars.price_format_dec_thousand, "");
			}
		});

		var blur_func = function(element_dom, disable_recalc_price) {
			var value_old = element_dom.value;
			element_dom.value = _this.get_element_object_value(element, "current_value_forced", element_dom.value);
			var is_number = _this.get_element_object_value(element, "is_number", 1) == 1;

			if (is_number) {
				element_dom.value = element_dom.value.replace(/[^0-9\.,-]/g, "");

				// check min/max
				var el_value_min = $element.data("min");
				var el_value_max = $element.data("max");

				if (element_dom.value != "") {
					var normalized_value = _this.normalize_value(element_dom.value);

					if (!isNaN(normalized_value)) {
						if (el_value_min != "" && !isNaN(el_value_min) && normalized_value < el_value_min) element_dom.value = el_value_min;
						if (el_value_max != "" && !isNaN(el_value_max) && normalized_value > el_value_max) element_dom.value = el_value_max;
					}
				}
			}

			// update price if value was changed
			var update_form = element_dom.value != value_old;
			var value_is_empty_or_zero = _this.is_empty_or_zero(element_dom.value);

			// don't show number 0 if disabled
			if (_this.form_vars[form_id].format_number_show_zero == 0 && value_is_empty_or_zero) {
				element_dom.value = "";
			}
			else {
				// format currency
				if (_this.get_element_object_value(element, "is_currency", 1) == 1) {
					var format_value = _this.normalize_value(element_dom.value);

					element_dom.value = _this.format_price(form_id, format_value, null, price_format, _this.form_vars[form_id].format_currency_numbers_elements, null, element);
				}
				// format value
				else {
					var text_before = _this.get_element_object_value(element, "text_before", "");
					var text_after  = _this.get_element_object_value(element, "text_after", "");

					element_dom.value = text_before + element_dom.value + text_after;
				}
			}

			_this.set_element_object_value(element, "current_value_formatted", element_dom.value);

			// update form
			if (update_form && !disable_recalc_price) {
				_this.form_change(null, form_id, $element);
			}

			// live summary
			_this.live_summary_update(form_id);

			// discount table highlight
			if (_this.get_element_object_value(element, "discount_show_table", 0) == 1 && _this.get_element_object_value(element, "discount_show_table_indicator", 1) == 1) {
				_this.discount_table_update(element);
			}
		};

		$element.on("blur", function() { blur_func(this); });
		$element.bind("ezfc_blur_trigger", function() { blur_func(this, true); });
	};

	/**
		checks if element is visible
	**/
	this.element_is_visible = function(element_id) {
		var element = this.get_element_object(element_id);
		if (!element) return false;

		var $element       = this.get_element_dom(element_id);
		var form_id        = element.form_id;
		var form_has_steps = this.form_vars[form_id].has_steps;
		var is_visible;

		// first check if element has custom hidden class
		if ($element.hasClass("ezfc-custom-hidden") || $element.hasClass("ezfc-hidden")) return false;

		// element is in group, check parent custom hidden class
		if (element.group_id != 0) {
			is_visible = $element.closest(".ezfc-custom-hidden, .ezfc-hidden").length < 1;
		}
		// element should be visible
		else {
			is_visible = true;
		}

		return is_visible;
	};

	/**
		checks if element can calculate due to visibility
	**/
	this.element_can_calculate_visibility = function(element_id) {
		var element = this.get_element_object(element_id);
		if (!element) return false;

		// calculate when hidden is disabled
		if (this.get_element_object_value(element, "calculate_when_hidden", 0) == 0) {
			// only calculate when element is visible
			return this.element_is_visible(element_id);
		}

		// calculate when hidden is true so always calculate
		return true;
	};

	this.clear_hidden_values = function(form) {
		var form_id = $(form).data("id");
		if (this.form_vars[form_id].clear_selected_values_hidden != 1) return;

		$(form).find(".ezfc-custom-hidden").each(function() {	
			_this.clear_hidden_values_element($(this).data("id"), true);
		});
	};

	this.clear_hidden_values_element = function(element_id, disable_change_trigger) {
		var element        = this.get_element_object(element_id);
		var $element       = this.get_element_dom(element_id);
		var $element_input = this.get_element_child_input_dom(element_id);

		if (!$element || !$element.length) return;

		if (element.type == "dropdown") {
			$element.find(":selected").removeAttr("selected");
		}
		else if (element.type == "radio") {
			this.radio_change_state($element.find(".ezfc-element-radio-input"), false, disable_change_trigger);
		}
		else if (element.type == "checkbox") {
			this.checkbox_change_state($element.find(".ezfc-element-checkbox-input"), false, disable_change_trigger);
		}
		else {
			$element_input.val("");
		}
	};

	this.escape_regex = function(s) {
		return s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
	};

	this.normalize_value = function(value, $element_dom, precision, disable_normalize) {
		value = String(value);
	
		if (typeof $element_dom !== "undefined") {
			var $element   = $($element_dom);
			var element_id = $element.data("id");
			var element    = this.get_element_object(element_id);

			var text_before = $.trim(this.get_element_object_value(element, "text_before", ""));
			var text_after  = $.trim(this.get_element_object_value(element, "text_after", ""));

			if (text_before != "") {
				var regex_text_before = _this.escape_regex(text_before);
				// replace prefix
				value = value.replace(new RegExp("^" + regex_text_before, ""), "");
			}
			if (text_after != "") {
				var regex_text_after = _this.escape_regex(text_after);
				// replace suffix
				value = value.replace(new RegExp(regex_text_after + "$", ""), "");
			}
		}

		// try to parse value
		var value_normalized = parseFloat(value);

		if (typeof precision !== "undefined" && (precision || precision == 0)) {
			precision = parseInt(precision);
			value_normalized = value_normalized.toFixed(precision);
		}

		if (!disable_normalize) {
			value_normalized = numeral(value).value();
		}

		return value_normalized;
	};

	this.set_submit_text = function(form, error) {
		var form_id = $(form).data("id");

		// default submit text
		var $submit_button = $(form).find(".ezfc-submit");
		var submit_type = "default";
		var submit_text = this.form_vars[form_id].submit_text.default;
		
		// price request
		if (this.form_vars[form_id].price_show_request == 1) {
			if (!this.form_vars[form_id].price_requested || error) {
				submit_text = this.form_vars[form_id].submit_text.request;
				submit_type = "request";
			}

			if (error) {
				this.price_request_toggle(form_id, false);
				return false;
			}
		}
		// summary
		else if (this.form_vars[form_id].summary_enabled == 1 && this.form_vars[form_id].summary_shown == 0) {
			submit_type = "summary";
			submit_text = this.form_vars[form_id].submit_text.summary;
		}
		// paypal
		else if (this.form_vars[form_id].use_paypal == 1) {
			submit_type = "paypal";
			submit_text = this.form_vars[form_id].submit_text.paypal;
		}
		// stripe
		else if (this.form_vars[form_id].payment_force_stripe == 1) {
			submit_type = "stripe";
			submit_text = this.form_vars[form_id].submit_text.stripe;
		}
		// authorize
		/*else if (this.form_vars[form_id].payment_force_authorize == 1) {
			submit_type = "authorize";
			submit_text = this.form_vars[form_id].submit_text.authorize;
		}*/
		// woocommerce
		else if (this.form_vars[form_id].use_woocommerce == 1) {
			submit_type = "woocommerce";
			submit_text = this.form_vars[form_id].submit_text.woocommerce;
		}
		// default text
		else {
			submit_text = this.form_vars[form_id].submit_text.default;

			// check is payment method is used and use payment submit text
			var payment_method_element = $(form).find(".ezfc-element-wrapper-payment");
			if (payment_method_element.length > 0) {
				var payment_method_value = $(payment_method_element).find(":checked").data("value");

				if (typeof this.form_vars[form_id].submit_text[payment_method_value] !== "undefined") {
					submit_type = payment_method_value;
					submit_text = this.form_vars[form_id].submit_text[payment_method_value];
				}
			}
		}

		$submit_button.val(submit_text);
		$submit_button.data("type", submit_type);
	};

	/**
		prevent enter key to trigger the click-event on step-buttons since pressing enter would submit the form and move a step backwards in the last step
	**/
	this.prevent_enter_step_listener = function($elements, $form) {
		// step prevent enter keypress
		$($elements).keypress(function(e) {
			// normalize
			var key = e.keyCode || e.which;

			if (e.which == 13) {
				//_this.form_submit($form, -1);
				e.preventDefault();
			}
		});
	};

	/**
		js hooks for advanced customization purposes
	**/
	this.call_hook = function(hook_name, args) {
		var func = window[hook_name];

		args = typeof args === "undefined" ? {} : args;

		if (typeof func !== "function") return args;

		return func(args);
	};

	/**
		stripe response handler
	**/
	this.stripe_response_handler = function(status, response) {
		var $form = $(".ezfc-form[data-id='" + _this.payment_form_id + "']");
		var $form_payment = $(".ezfc-payment-form[data-form_id='" + _this.payment_form_id + "']");

		if (response.error) {
			_this.debug_message(response.error.message);

			$("#ezfc-payment-message-" + _this.payment_form_id).text(response.error.message);
			$form_payment.find(".ezfc-payment-submit, .ezfc-payment-cancel").prop("disabled", false);

			setTimeout(function() {
				$("#ezfc-payment-message-" + _this.payment_form_id).text("");
			}, 7500);
		}
		else {
			// Get the token ID:
			var token = response.id;

			// Insert the token ID into the form so it gets submitted to the server:
			$form.find("#ezfc-stripetoken-" + _this.payment_form_id).val(token);

			// Submit the form:
			_this.form_submit($form, -1, "stripe-checkout");
		}
	};

	/**
		authorize create token
	**/
	this.authorize_create_token = function(id) {
		var secureData = {}; authData = {}; cardData = {};
		var $form_payment = $("#ezfc-authorize-form-" + id);

		cardData.cardNumber = $("#ezfc-element-payment-authorize-card-number-" + id).val();
		cardData.cardNumber = cardData.cardNumber.replace(/ /g, ""); // remove whitespaces
		cardData.month      = $("#ezfc-element-payment-authorize-expiry-month-" + id).val();
		cardData.year       = $("#ezfc-element-payment-authorize-expiry-year-" + id).val();
		cardData.cardCode   = $("#ezfc-element-payment-authorize-cvc-" + id).val();
		secureData.cardData = cardData;

		authData.clientKey  = ezfc_vars.authorize.client_key;
		authData.apiLoginID = ezfc_vars.authorize.api_login_id;
		secureData.authData = authData;

		Accept.dispatchData(secureData, _this.authorize_response_handler);
	};

	/**
		authorize response handler
	**/
	this.authorize_response_handler = function(response) {
		var $form = $(".ezfc-form[data-id='" + _this.payment_form_id + "']");
		var $form_payment = $(".ezfc-payment-form[data-form_id='" + _this.payment_form_id + "']");
		var form_id = $form.data("id");

		if (response.messages.resultCode === "Error") {
			$form_payment.find(".ezfc-payment-submit, .ezfc-payment-cancel").prop("disabled", false);

			var error_messages = [];
			for (var i = 0; i < response.messages.message.length; i++) {
				error_messages.push(response.messages.message[i].text);
				_this.debug_message(response.messages.message[i].code + ": " + response.messages.message[i].text);
			}

			_this.set_message(form_id, error_messages.join(" "));
		}
		else {
			var token = response.opaqueData.dataValue;
			$("#ezfc-authorizetoken-" + _this.payment_form_id).val(token);

			_this.form_submit($form, -1, "authorize-checkout");
		}
	};

	/**
		checks if a date is valid
	**/
	this.is_valid_date = function(d) {
		return d && typeof d === "object" && typeof d.setHours === "function";
	};

	/**
		checks for blocked days in a date-element
	**/
	this.check_datepicker_days = function(date, available_days, blocked_days) {
		if (!date || typeof date !== "object" || typeof date.setHours !== "function") {
			return [false, ""];
		}

		// datepicker date
		date.setHours(0,0,0,0);
		var loop_date = date.getTime();

		if (blocked_days.length) {
			for (var bd in blocked_days) {
				blocked_days[bd] = $.trim(blocked_days[bd]);
				var is_period = blocked_days[bd].indexOf(":") !== -1;

				// date period
				if (is_period) {
					var blocked_days_period = blocked_days[bd].split(":");

					if (blocked_days_period.length == 2) {
						var blocked_days_period_begin = new Date(blocked_days_period[0]);
						var blocked_days_period_end   = new Date(blocked_days_period[1]);
						blocked_days_period_begin.setHours(0,0,0,0);
						blocked_days_period_end.setHours(0,0,0,0);

						// loop date is between the period -> block date
						if (loop_date >= blocked_days_period_begin.getTime() && loop_date <= blocked_days_period_end.getTime()) {
							return [false, ""];
						}
					}
				}
				// single date
				else {
					var blocked_day = new Date(blocked_days[bd]);
					blocked_day.setHours(0,0,0,0);

					// loop date is blocked day
					if (loop_date == blocked_day.getTime()) {
						return [false, ""];
					}
				}
			}
		}

		var day = date.getDay();
		return [$.inArray(day, available_days) !== -1, ''];
	};

	/**
		checks datepicker predefined values
	**/
	this.check_datepicker_predefined_values = function($element) {
		var input_date = $element.val();
		var set_date   = false;

		// skip hidden
		if (input_date == "") return;

		switch (input_date) {
			case "__today__":
				set_date = new Date();
			break;
			case "__tomorrow__":
				set_date = new Date(new Date().getTime() + 86400000);
			break;
			case "__current_month__":
				set_date = new Date().getMonth();
			break;
			case "__current_year__":
				set_date = new Date().getFullYear();
			break;
			default:
				set_date = input_date;
			break;
		}

		if (set_date) {
			$element.datepicker("setDate", set_date);
		}
	};

	/**
		check basic inline math and return calculated value
	**/
	this.check_inline_math = function(custom_value, cond_target) {
		var inline_math_func = function() {
			
		};

		return custom_value;

		/*// basic math operators only
		var math_operators = ["=", "+", "-", "*", "/", "s"];
		//var math_operators_regex = /([\=\+\-\*\/s][+-]?([0-9]*[.])?[0-9]+)/g;
		var math_regex_operators = /[\=\+\-\*\/s][+-]?/g;
		var math_regex_numbers = /[0-9]+[\.]?[0-9]+/g;
		// trim target value
		custom_value = $.trim(custom_value);

		// split chars

		if (math_array.length) {
			var target_value = _this.get_value_from_element(cond_target, null, false, true, { return_value: "base" });
			var custom_value_new = target_value;

			for (var i in math_array) {
				if (!math_array[i]) continue;

				// check first char for inline math
				var first_char = math_array[i].toString().charAt(0);
				// inline math for conditional set action
				if ($.inArray(first_char, math_operators) === -1) continue;

				var value_sanitized = math_array[i].substring(1);
				
				if (!isNaN(value_sanitized)) {
					value_sanitized = parseFloat(value_sanitized);

					if (first_char == "=")      custom_value_new = value_sanitized;
					else if (first_char == "+") custom_value_new += value_sanitized;
					else if (first_char == "-") custom_value_new -= value_sanitized;
					else if (first_char == "*") custom_value_new *= value_sanitized;
					else if (first_char == "/") custom_value_new /= value_sanitized;
					else if (first_char == "s") custom_value_new *= -1;

					custom_value = custom_value_new;
				}
			}
		}

		return custom_value;*/
	};

	/**
		get subtotal value from target
	**/
	this.get_target_subtotal_value = function(form_id, element_id, use_previous_element_price) {
		for (var i in this.subtotals[form_id]) {
			if (this.subtotals[form_id][i].el_id == element_id) {
				if (use_previous_element_price) {
					// if previous element doesn't exist, return 0
					return typeof this.subtotals[form_id][i-1] !== "undefined" ? this.subtotals[form_id][i-1].price : 0;
				}
				else {
					return this.subtotals[form_id][i].price;
				}
			}
		}

		return null;
	};

	this.get_calculated_element_value = function(form_id, element_id) {
		var $form          = $("#ezfc-form-" + form_id);
		var element        = this.get_element_object(element_id);
		var $element       = this.get_element_dom(element_id);
		var calc_activated = this.get_element_object_value(element, "calculate_enabled", 1);

		// repeatable element
		if (this.is_repeatable_element(element_id) || this.has_repeatable_parent_id(element_id)) return this.calculate_element_loop(element_id, 0, true);

		// return 0 if disabled
		if (calc_activated == 0) return 0;

		// calculations
		var calculated_value = this.get_element_object_value(element, "current_value_forced", this.get_element_object_value(element, "current_value", 0));

		return calculated_value;
	};

	/**
		set message
	**/
	this.set_message = function(form_id, message) {
		var $form = $("#ezfc-form-" + form_id);
		if (!$form) return false;

		var $form_wrapper = $form.closest(".ezfc-wrapper");
		var $message_wrapper;

		// use payment message element
		if ($form_wrapper.find(".ezfc-payment-dialog-open").length > 0) {
			$message_wrapper = $form_wrapper.find(".ezfc-payment-errors");
		}
		else {
			$message_wrapper = $("#ezfc-message-" + form_id);
		}

		if (!$message_wrapper.length && console) {
			console.log(message);
			return false;
		}

		$message_wrapper.text(message).fadeIn().delay(7500).fadeOut();
	};

	/**
		reload recaptcha
	**/
	this.recaptcha_reload = function() {
		var has_recaptcha = $(".ezfc-form .g-recaptcha").length;

		if (has_recaptcha && typeof grecaptcha !== "undefined") {
			grecaptcha.reset();
		}
	};

	/**
		populate html placeholders
	**/
	this.populate_html_placeholders = function($form) {
		$form.find(".ezfc-html-placeholder").each(function(i, el) {
			var $el = $(el);
			var $element_wrapper = $el.closest(".ezfc-custom-element");
			var element_id = $element_wrapper.data("id");
			var element = _this.get_element_object(element_id);
			var value = "";

			var target_id = _this.functions.get_element_id_by_name($form.data("id"), $el.data("listen_target"));
			// no target found
			if (!target_id) return;

			// retrieve value
			var retrieve_value = $el.data("listen_retrieve");
			value = _this.get_value_from_element(false, target_id, true, false, { return_value: retrieve_value });

			var replace_placeholders_show_zero = _this.get_element_object_value(element, "replace_placeholders_show_zero", 1);
			// return empty string
			if (replace_placeholders_show_zero == 0 && value == 0) {
				value = "";
			}

			// join values together if array
			if (value && typeof value === "object" && typeof value.join === "function") value = value.join(", ");
			// trim value
			value = $.trim(value);

			$el.text(value);
		});
	};

	/**
		init repeatable form
	**/
	this.init_repeatable_group = function(group_id) {
		var element  = this.get_element_object(group_id);
		var $element = this.get_element_dom(group_id);

		this.group_check_actions(group_id);
	};

	/**
		repeat group
	**/
	this.group_repeat = function(group_id) {
		var element  = this.get_element_object(group_id);

		// check count
		var repeatable_count_max = parseInt(this.get_element_object_value(element, "repeatable_count_max", 0));
		var repeatable_count = this.get_element_object_value(element, "__repeated_count", 0);
		// max count reached
		if (repeatable_count_max > 0 && repeatable_count >= repeatable_count_max - 1) return;

		// group repeat id counter
		if (typeof this.group_repeat_elements[group_id] === "undefined") {
			this.group_repeat_elements[group_id] = 0;
		}
		this.group_repeat_elements[group_id]++;
		// increase element counter
		this.set_element_object_value(element, "__repeated_count", ++repeatable_count);

		// group wrapper
		var $group = $("#ezfc_element-" + group_id);

		// remove events first
		$group.find(".ezfc-spinner").each(function() {
			if ($(this).spinner("instance")) $(this).spinner("destroy");
		});
		$group.find(".ezfc-slider-element").each(function() {
			if ($(this).slider("instance")) $(this).slider("destroy");
		});

		var $clone   = $group.clone(true);
		var clone_id = $clone.attr("id") + "-" + this.group_repeat_elements[group_id];
		var $form    = $group.closest(".ezfc-form");
		var form_id  = $form.data("id");

		$clone.find(".ezfc-group-repeatable-wrapper").remove();

		var group_id_index = this.form_vars[form_id].form_elements_order.indexOf(String(group_id));
		var group_elements = this.get_element_object_value(element, "__repeated_ids", []);

		// change id
		$clone.attr("id", clone_id);
		$clone.hide();
		// reset elements to default values
		this.reset_elements($clone);
		// append to form elements wrapper
		$clone.appendTo($("#ezfc-repeatable-wrapper-" + group_id + " .ezfc-repeatable-form-elements")).fadeIn(500);

		// change IDs
		var new_group_elements_ids  = [];
		var new_group_elements_data = [];
		$clone.find("[id^='ezfc_element-']").each(function() {
			var element_id = $(this).data("id");
			var old_id     = $(this).attr("id");
			var new_id     = old_id + "-" + _this.group_repeat_elements[group_id];
			var new_fe_id  = $(this).data("id") + "-" + _this.group_repeat_elements[group_id];		

			// wrapper
			if ($(this).hasClass("ezfc-custom-element")) {
				new_group_elements_ids.push(new_fe_id);
				new_group_elements_data.push({
					new_element_id: new_fe_id,
					old_element_id: element_id,
					repeat_index: _this.group_repeat_elements[group_id],
				});
			}
			// input options
			else {
				var element_input_name = $(this).attr("name");
				if (!element_input_name) return;

				// modify name
				var names_array = element_input_name.replace(/\]/g, "").split("[").map(function(part) {
					return part.replace(/\[/g, "");
				});

				if (names_array.length < 3) return;

				var input_name_begin = names_array[0];
				names_array = names_array.slice(1, names_array.length);
				
				var counter_id = _this.group_repeat_elements[group_id] + parseInt(names_array[2]);
				names_array[2] = counter_id;
				var new_name = input_name_begin + "[" + names_array.join("][") + "]";

				$(this).attr("name", new_name);
			}

			$(this).attr("id", new_id);
			$(this).data("id", new_fe_id);

			// update labels
			var label_selector_old_id = "label[for='" + old_id + "']";
			$clone.find(label_selector_old_id).each(function() {
				$(this).attr("for", new_id);
			});
		});

		// update table order elements
		$clone.find(".ezfc-element-table_order").each(function() {
			// buttons
			$clone.find(".ezfc-table_order-btn").each(function() {
				var target = $(this).siblings(".ezfc-element-table_order-quantity-input").attr("id");
				$(this).data("target", target);
			});

			// subtotal ids
			$clone.find(".ezfc-element-table_order-subtotal").each(function() {
				var row_id = $(this).closest("tr[data-row]").data("row");
				var subtotal_id_old = $(this).attr("id");
				var subtotal_id_new = subtotal_id_old + "-" + _this.group_repeat_elements[group_id] + "-" + row_id;

				$(this).attr("id", subtotal_id_new);
			});
		});

		// add elements to form elements order
		this.form_vars[form_id].form_elements_order.splice.apply(this.form_vars[form_id].form_elements_order, [group_id_index, 0].concat(new_group_elements_ids));

		// add to repeatable form element data
		var repeated_ids = this.get_element_object_value(element, "__repeated_ids", []);
		repeated_ids = this.unique(repeated_ids.concat(new_group_elements_ids));
		this.set_element_object_value(element, "__repeated_ids", repeated_ids);

		// add to form elements cache
		for (var i in new_group_elements_data) {
			var old_element_id = new_group_elements_data[i].old_element_id;
			var new_element_id = new_group_elements_data[i].new_element_id;

			// clone object
			var cloned_element = _this.get_element_object(old_element_id);
			if (!cloned_element) continue;

			var new_element_object = $.extend(true, {}, cloned_element);
			new_element_object.id = new_element_id;
			ezfc_vars.element_vars[new_element_object.form_id][new_element_object.id] = new_element_object;

			// add to cache
			_this.add_form_element_to_cache(new_element_id, new_element_object.form_id);

			// change target elements from calculation/condition
			if (new_element_object.hasOwnProperty("calculate")) {
				for (var ica in new_element_object.calculate) {
					var calc_row = new_element_object.calculate[ica];

					if (_this.form_vars[form_id].form_elements_order.indexOf(String(calc_row.target)) !== -1) {
						calc_row.target = calc_row.target + "-" + new_group_elements_data[i].repeat_index;
					}
				}
			}

			if (new_element_object.hasOwnProperty("conditional")) {
				for (var it in new_element_object.conditional.target) {
					var cond_target = String(new_element_object.conditional.target[it]);

					if (_this.form_vars[form_id].form_elements_order.indexOf(cond_target) !== -1) {
						new_element_object.conditional.target[it] = cond_target + "-" + new_group_elements_data[i].repeat_index;
					}
				}
			}
		}

		// re-init events
		$group.find(".ezfc-spinner").each(function() { _this.init_spinner($(this)); });
		$group.find(".ezfc-slider").each(function() { _this.init_slider($(this)); });
		$clone.find(".ezfc-spinner").each(function() { _this.init_spinner($(this)); });
		$clone.find(".ezfc-slider").each(function() { _this.init_slider($(this)); });

		this.init_table_order(form_id);
		this.form_change($form);
		this.group_check_actions(group_id);
	};

	this.group_remove = function(group_id) {
		// check length
		var $repeated_elements = $("#ezfc-repeatable-wrapper-" + group_id).find(".ezfc-repeatable-form-elements > .ezfc-custom-element");
		var repeated_length = $repeated_elements.length;

		if (repeated_length <= 1) return;

		var element = this.get_element_object(group_id);

		// decrease counter
		var repeatable_count = this.get_element_object_value(element, "__repeated_count", 0);
		this.set_element_object_value(element, "__repeated_count", --repeatable_count);

		// get last repeated element row
		var $last_element = $repeated_elements.last();
		var index         = this.get_repeatable_element_counter_from_dom_id($last_element.attr("id"));
		var $group        = $("#ezfc_element-" + group_id + "-" + index);
		var $form         = $group.closest(".ezfc-form");

		// remove triggers
		for (var ie in element.__element_ids) {
			var find_id = element.__element_ids[ie] + "-" + index;

			var find_element_in_repeated_ids = element.__repeated_ids.indexOf(find_id);
			if (find_element_in_repeated_ids > -1) {
				element.__repeated_ids.splice(find_element_in_repeated_ids, 1);
			}

			var find_element_in_form_elements_order = this.form_vars[element.form_id].form_elements_order.indexOf(find_id);
			if (find_element_in_form_elements_order > -1) {
				this.form_vars[element.form_id].form_elements_order.splice(find_element_in_form_elements_order, 1);
			}
		}

		$group.fadeOut(300, function() {
			$(this).remove();
		});

		this.form_change($form);
		this.group_check_actions(group_id);
	};

	this.group_check_actions = function(group_id) {
		var element  = this.get_element_object(group_id);
		var $element = $("#ezfc-repeatable-wrapper-" + group_id);

		var repeatable_count_max = parseInt(this.get_element_object_value(element, "repeatable_count_max", 0));
		var repeatable_count = this.get_element_object_value(element, "__repeated_count", 0);

		var btn_disabled_class = "ezfc-btn-disabled";
		var $btn_repeat_group = $element.find(".ezfc-repeatable-form-repeat-button");
		var $btn_remove_group = $element.find(".ezfc-repeatable-form-remove-button");

		if (repeatable_count == 0) {
			$btn_repeat_group.removeAttr("disabled");
			$btn_remove_group.attr("disabled", "disabled");
		}
		// max count reached
		else if (repeatable_count_max > 0 && repeatable_count >= repeatable_count_max - 1) {
			$btn_repeat_group.attr("disabled", "disabled");
			$btn_remove_group.removeAttr("disabled");
		}
		else {
			$btn_repeat_group.removeAttr("disabled");
			$btn_remove_group.removeAttr("disabled");
		}
	};

	/**
		toggle group
	**/
	this.toggle_group = function($group_wrapper, force_show) {
		var $group = $group_wrapper.find("> .ezfc-group-elements").first();

		if ($group_wrapper.hasClass("ezfc-group-collapsible")) {
			$group.slideToggle(500);

			var group_class_active = "ezfc-group-active";
			var icon_class_open    = "fa-chevron-circle-down";
			var icon_class_closed  = "fa-chevron-circle-right";

			var $toggle_icon = $group_wrapper.find(".ezfc-collapse-icon i").first();
			// close group
			if ($toggle_icon.hasClass(icon_class_open) && !force_show) {
				$toggle_icon.removeClass(icon_class_open).addClass(icon_class_closed);
				$group_wrapper.removeClass(group_class_active);
			}
			// open group
			else {
				$toggle_icon.removeClass(icon_class_closed).addClass(icon_class_open);
				$group_wrapper.addClass(group_class_active);
			}
		}
	};

	/**
		init tips
	**/
	this.init_tips = function() {
		$(".ezfc-element-description[data-ezfctip]").each(function() {
			var message = $(this).data("ezfctip");

			var tip = new Opentip($(this), {
				background: ezfc_vars.opentip.background || "yellow",
				borderWidth: 0,
				closeButtonCrossColor: ezfc_vars.opentip.color || "black",
				redraw: true,
				removeElementsOnHide: true,
				target: $(this),
				tipJoint: ezfc_vars.required_text_position || "middle right",
			});

			tip.setContent(message);
		});
	};

	/**
		init table order
	**/
	this.init_table_order = function(form_id) {
		// loop through table order elements
		//for (var i in ezfc_vars.element_vars[form_id]) {
		for (var i in this.form_vars[form_id].form_elements_order) {
			var element_id = this.form_vars[form_id].form_elements_order[i];
			var element    = this.get_element_object(element_id);
			var $element   = this.get_element_dom(element_id);

			// skip other elements
			if (element.type != "table_order") continue;

			// format price for each row
			for (var r in element.table_order) {
				var row_item  = element.table_order[r];
				var row_price = row_item.option.value;

				var $row = $element.find(".ezfc-element-table_order-row[data-row='" + r + "']");
				var $row_price_dom = $row.find(".ezfc-element-table_order-subtotal");

				// format price
				var row_price_formatted = _this.format_price(form_id, row_price, null, null, element.is_currency);
				var text_prefix         = _this.get_element_object_value(row_item.option, "text_prefix", "");
				var text_suffix         = _this.get_element_object_value(row_item.option, "text_suffix", "");
				
				$row_price_dom.text(text_prefix + row_price_formatted + text_suffix);
			}
		}
	};

	this.update_table_order = function(element_id, form_id) {
		var element = this.get_element_object(element_id);

		var total = _this.get_element_object_value(element, "current_value", 0);
		var total_formatted = this.format_price(form_id, total, null, null, element.is_currency);
		
		$("#ezfc_element-" + element_id + "-footer-price").text(total_formatted);
	};

	this.update_table_order_all = function(form_id) {
		for (var i in this.form_vars[form_id].form_elements_order) {
			var element_id = this.form_vars[form_id].form_elements_order[i];
			var element    = this.get_element_object(element_id);

			if (element.type != "table_order") continue;

			this.update_table_order(element_id, form_id);
		}
	};

	/**
		show tip
	**/
	this.show_tip = function(el_tip, el_target, tip_delay, message) {
		var tip = new Opentip(el_tip, {
			background: ezfc_vars.opentip.background || "yellow",
			closeButtonCrossColor: ezfc_vars.opentip.color || "black",
			delay: tip_delay,
			hideDelay: 0.1,
			hideTriggers: ["closeButton", "target"],
			removeElementsOnHide: true,
			showOn: null,
			target: el_target,
			tipJoint: ezfc_vars.required_text_position || "middle right",
		});

		tip.setContent(message);
		tip.show();
	};

	/**
		cancel submission
	**/
	this.submit_cancel = function($form, buttons_only) {
		var $form_wrapper   = $form.closest(".ezfc-wrapper");
		var $submit_element = $form.find("input[type='submit']");
		var $submit_icon    = $form.find(".ezfc-submit-icon");
		var id              = $form.data("id");

		$submit_icon.fadeOut();
		$submit_element.prop("disabled", false);
		$form_wrapper.find(".ezfc-payment-submit, .ezfc-payment-cancel").prop("disabled", false);

		for (var i in this.payment_methods) {
			var payment_method = _this.payment_methods[i];

			_this.form_vars[id].payment_info_shown[payment_method] = 0;
		}
	};

	/**
		open form in popup
	**/
	this.popup_form_open = function(form_id) {
		var $form = $("#ezfc-form-popup-" + form_id);
		if (!$form.length) return;

		// move/append to body
		var $modal = $(".ezfc-modal");
		var $form_parent = $form.parent();
		if ($form_parent.length && $form_parent[0].tagName != "body") {
			$form.appendTo("body");
			$modal.appendTo("body");
		}

		$modal.addClass("ezfc-modal-open");
		$form.fadeIn();
	};

	/**
		close popup form
	**/
	this.popup_form_close = function(form_id) {
		$(".ezfc-modal").removeClass("ezfc-modal-open");
		$("#ezfc-form-popup-" + form_id).fadeOut();
	};


	/**
		debug
	**/
	this.remove_debug_info = function() {
		$(".ezfc-debug-info").remove();
	};

	this.add_debug_info = function(type, element, text) {
		if (ezfc_vars.debug_mode != 2) return;

		type = type || $(element).data("element");
		// don't show group elements
		if (type == "group") return;

		var id = $(element).attr("id");
		if (id) {
			id = id.split("ezfc_element-")[1];
		}

		var debug_el_id = "ezfc-debug-" + id;
		var debug_exists_for_element = $("#" + debug_el_id).length;

		// check if wrapper was already added
		if (type == "custom_calculation" && debug_exists_for_element) return;

		var debug_text = "[[" + type + " #" + id + "]]\n[" + type + "]\n" + text + "\n\n";
		if (debug_exists_for_element) {
			$(element).find(".ezfc-debug-info").append(debug_text);
		}
		else {
			$(element).append("<pre id='" + debug_el_id + "' class='ezfc-debug-info ezfc-debug-type-" + type + "'>" + debug_text + "</pre>");
		}

		console.log(text, element);
	};

	this.debug_message = function(message, level) {
		level = typeof level === "undefined" ? 1 : level;

		if (ezfc_vars.debug_mode == 0 || ezfc_vars.debug_mode < level) return;

		console.log(message);
	};

	/**
		misc
	**/
	this.jqueryui_date_diff = function(start_date, end_date, workdays_only, element, discount_value_type) {
		if (!start_date || !end_date) return 0;

		// clone date objects
		var start = new Date(start_date);
		var end   = new Date(end_date);

		var days = 0;
		var days_price = 0;
		var days_use_single_prices = false;
		var datepicker_day_prices = [];
		var datepicker_day_prices_default = false;
		var datepicker_day_prices_raw = false;
		discount_value_type = discount_value_type || "calculated";
		var full_days_only = this.get_element_object_value(element, "daterange_count_full_days", 0);

		// count full days
		if (full_days_only == 1) {
			end = end.setDate(end.getDate() + 1);
		}

		if (element) {
			datepicker_day_prices_default = this.get_element_object_value(element, "datepicker_day_prices_default", false);
			datepicker_day_prices_raw     = this.get_element_object_value(element, "datepicker_day_prices", false);

			if (typeof datepicker_day_prices_default === "object") {
				datepicker_day_prices_default = $.map(datepicker_day_prices_default, parseFloat);
			}

			// check for defined date ranges
			if (typeof datepicker_day_prices_raw === "object") {
				// yyyy-mm-dd : yyyy-mm-dd = 10, 20, 30, 40, 50, 60, 70
				for (var i in datepicker_day_prices_raw) {
					var datepicker_day_prices_text_array_row = datepicker_day_prices_raw[i].split("=");

					if (datepicker_day_prices_text_array_row.length > 1) {
						days_use_single_prices = true;

						var row_dates = datepicker_day_prices_text_array_row[0].split(":");
						row_dates[0] = new Date(row_dates[0]);
						row_dates[1] = new Date(row_dates[1]);

						var day_prices = $.map(datepicker_day_prices_text_array_row[1].split(","), parseFloat);

						datepicker_day_prices.push({
							start:  row_dates[0],
							end:    row_dates[1],
							prices: day_prices,
						});
					}
				}
			}
		}

	    while (start < end) {
	    	var day = start.getUTCDay();
	    	var day_price = this.dates_get_single_price(datepicker_day_prices, start);

	    	var add_day = false;

	    	if (workdays_only) {
		        // skip sun / sat
		        if (day !== 6 && day !== 7) {
		        	add_day = true;
		        }
		    }
		    else {
		    	add_day = true;
		    }

		    if (add_day) {
		    	days++;
		    }

		    // use day price
            if (day_price != -1) {
            	days_price += day_price;
            }
            // use default
            else if (datepicker_day_prices_default && typeof datepicker_day_prices_default[day] !== "undefined") {
            	days_price += datepicker_day_prices_default[day];
            }

	        // increase by 1 day
	        start.setDate(start.getDate() + 1);
	    }

		// return days
		if (!days_use_single_prices || discount_value_type == "raw") return days;

		// return days price
		return days_price;
	};

	this.dates_get_single_price = function(date_array, day) {
		for (var i in date_array) {
			var date_row_object = date_array[i];

			if (day >= date_row_object.start && day <= date_row_object.end) {
				var utc_day = day.getUTCDay();

				// day price found
				if (typeof date_row_object.prices[utc_day] !== "undefined") {
					return date_row_object.prices[utc_day];
				}
				// no day price found, use first value
				else if (date_row_object.prices[0] !== "undefined") {
					return date_row_object.prices[0];
				}
			}
		}

		return -1;
	};

	this.roundTo = function(number, precision) {
		precision = Math.max(0, parseInt(precision));
		var factor = Math.pow(10, precision);
		var tempNumber = number * factor;
		var roundedTempNumber = Math.round(tempNumber);
		return roundedTempNumber / factor;
	};

	this.sprintf = function() {
		var args = arguments,
	    string = args[0],
	    i = 1;
	    return string.replace(/%((%)|s|d)/g, function (m) {
	        // m is the matched format, e.g. %s, %d
	        var val = null;
	        if (m[2]) {
	            val = m[2];
	        } else {
	            val = args[i];
	            // A switch statement so that the formatter can be extended. Default is %s
	            switch (m) {
	                case '%d':
	                    val = parseFloat(val);
	                    if (isNaN(val)) {
	                        val = 0;
	                    }
	                    break;
	            }
	            i++;
	        }
	        return val;
	    });
	};

	this.throttle = function(fn, threshhold, scope) {
	  threshhold = threshhold || (threshhold = 100);
	  var last,
	      deferTimer;
	  return function () {
	    var context = scope || this;

	    var now = +new Date(),
	        args = arguments;
	    if (last && now < last + threshhold) {
	      // hold on to it
	      clearTimeout(deferTimer);
	      deferTimer = setTimeout(function () {
	        last = now;
	        fn.apply(context, args);
	      }, threshhold);
	    } else {
	      last = now;
	      fn.apply(context, args);
	    }
	  };
	};

	this.unique = function(array) {
		var array_unique = [];
		$.each(array, function(i, el){
		    if ($.inArray(el, array_unique) === -1) array_unique.push(el);
		});

		return array_unique;
	};

	this.has_class = function($element, classes) {
		for (var i in classes) {
			if ($element.hasClass(classes[i])) return true;
		}

		return false;
	};

	this.is_empty = function(value) {
		return $.trim(value) === "";
	};

	this.is_empty_or_zero = function(value) {
		return this.is_empty(value) || value == 0;
	};

	this.object_has_property = function(obj, key) {
		if (typeof obj !== "object" || obj === null) return false;

		return obj.hasOwnProperty(key);
	};

	this.generate_ref_id = function() {
		function s4() {
			return Math.floor((1 + Math.random()) * 0x10000)
			  .toString(16)
			  .substring(1);
		}
		return s4() + s4() + s4() + s4();
	};

	this.set_ref_id = function(form_id) {
		var ref_id = this.generate_ref_id();
		$("#ezfc-ref-id-" + form_id).val(ref_id);
	};

	this.get_calculation_routine_by_name = function(element, name) {
		if (!element.hasOwnProperty("calculate_routine")) return false;

		name = $.trim(name);

		var calculation_routines = [];
		for (var i in element.calculate_routine) {
			if (element.calculate_routine[i].name == name) {
				calculation_routines.push(parseInt(i));
			}
		}

		return calculation_routines;
	};

	this.file_upload_attach_event = function(el, data, $btn, e) {
		if ($(el).val() == "") return false;

		data.files = data.originalFiles;

		data.submit();
		$($btn).attr("disabled", "disabled");

		e.preventDefault();
		return false;
	};

	this.init();
};

ezfc_functions = {};
var EZFC;

jQuery(document).ready(function($) {
	EZFC = new EZFC_Object($);
});

}