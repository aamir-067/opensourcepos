<script type="text/javascript">
	// live clock
	var clock_tick = function clock_tick() {
		setInterval('update_clock();', 1000);
	}

	// start the clock immediately
	clock_tick();

	var update_clock = function update_clock() {
		document.getElementById('liveclock').innerHTML = moment().format("<?php echo dateformat_momentjs($config['dateformat'] . ' ' . $config['timeformat']) ?>");
	}

	$.notifyDefaults({ placement: {
		align: "<?php echo esc($config['notify_horizontal_position'], 'js') ?>",
		from: "<?php echo esc($config['notify_vertical_position'], 'js') ?>"
	}});

	var cookie_name = "<?php echo esc(config('Cookie')->prefix, 'js') . esc(config('Security')->cookieName, 'js') ?>";

	var csrf_token = function() {
		return Cookies.get(cookie_name);
	};

	var csrf_form_base = function() {
		return { <?php echo esc(config('Security')->tokenName, 'js') ?> : function () { return csrf_token() } }
	};

	var setup_csrf_token = function() {
		$('input[name="<?php echo esc(config('Security')->tokenName, 'js') ?>"]').val(csrf_token());
	};

	var ajax = $.ajax;

	$.ajax = function() {
		var args = arguments[0];
		if (args['type'] && args['type'].toLowerCase() == 'post' && csrf_token()) {
			if (typeof args['data'] === 'string')
			{
				args['data'] += '&' + $.param(csrf_form_base());
			}
			else
			{
				args['data'] = $.extend(args['data'], csrf_form_base());
			}
		}

		return ajax.apply(this, arguments);
	};
    
	$(document).ajaxComplete(setup_csrf_token);
	$(document).ready(function(){
		$("#logout").click(function(event) {
			event.preventDefault();
			$.ajax({
				url: "<?php echo site_url('home/logout'); ?>", 
				data: { 
					"<?php echo esc(config('Security')->tokenName, 'js'); ?>": csrf_token()
				},
				success: function() {
					window.location.href = '<?php echo site_url(); ?>';
				},
				method: "POST"
			});
		});
	});

	var submit = $.fn.submit;

	$.fn.submit = function() {
		setup_csrf_token();
		submit.apply(this, arguments);
	};
</script>