<script type="text/javascript">
	jQuery(document).ready(function($) {
		function saveForm() {
			$('#save-success').hide();
			
			var formData = $('#ql-engage-settings-apikey').serialize();
			formData += '&action=save_engage_settings_apikey';
			
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url('admin-ajax.php') ?>',
				data: formData,
				success: function(message) {
					if (message == 'success') {
						$('#save-success').show();
					}
				}
			});
		}
		
		$('#save-settings').click(function() {
			saveForm();
			return false;
		});
		
		$('#ql-engage-settings-apikey').submit(function() {
			saveForm();
			return false;
		});
	});
</script>

<div class="wrap">
	<h1 class="wp-heading-inline">Questline Engage</h1>
	<hr class="wp-header-end">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>admin.php?page=ql-engage-settings-apikey">API Key</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=ql-engage-settings-shortcodes">Shortcodes</a>
	</h2>
	
	<div id="save-success" class="notice notice-success is-dismissible" style="display: none;">
		<p>The API key has been saved.</p>
	</div>
	
	<form id="ql-engage-settings-apikey" style="margin-top: 20px; font-size: 14px;">
		<?php wp_nonce_field('ql_engage_settings_apikey_form') ?>
		
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="ql_engage_settings_apikey">Engage API Key</label>
				</th>
				<td>
					<input type="text" class="regular-text" id="ql_engage_settings_apikey" name="ql_engage_settings_apikey" value="<?php echo get_option('ql_engage_settings_apikey') ?>" />
					<p class="description">Enter your API key to access Questline Engage content</p>
				</td>
			</tr>
		</table>
		
		<a id="save-settings" href="#" class="button button-primary">Save</a>
	</form>
</div>
