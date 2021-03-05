<script type="text/javascript">
	jQuery(document).ready(function($) {
		function saveForm() {
			$('#save-success').hide();
			
			var formData = $('#ql-engage-settings-shortcodes').serialize();
			formData += '&action=save_engage_settings_shortcodes';
			
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
		
		$('#ql-engage-settings-shortcodes').submit(function() {
			saveForm();
			return false;
		});
	});
</script>

<div class="wrap">
	<h1 class="wp-heading-inline">Questline Engage</h1>
	<hr class="wp-header-end">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=ql-engage-settings-apikey">API Key</a>
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>admin.php?page=ql-engage-settings-shortcodes">Shortcodes</a>
	</h2>
	
	<div id="save-success" class="notice notice-success is-dismissible" style="display: none;">
		<p>The shortcode settings have been saved.</p>
	</div>
	
	<form id="ql-engage-settings-shortcodes" style="margin-top: 20px; font-size: 14px;">
		<?php wp_nonce_field('ql_engage_settings_shortcodes_form') ?>
		
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="ql_engage_settings_shortcodes_display_titles">Display article titles</label>
				</th>
				<td>
					<?php $display_titles_checked = (get_option('ql_engage_settings_shortcodes_display_titles') == 'on') ? 'checked' : '' ?>
					<input type="checkbox" id="ql_engage_settings_shortcodes_display_titles" name="ql_engage_settings_shortcodes_display_titles" <?php echo $display_titles_checked ?> />
					<p class="description">Determines whether or not to show the Engage article title in the embedded article HTML.</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ql_engage_settings_shortcodes_display_published_dates">Display published dates</label>
				</th>
				<td>
					<?php $display_published_dates_checked = (get_option('ql_engage_settings_shortcodes_display_published_dates') == 'on') ? 'checked' : '' ?>
					<input type="checkbox" id="ql_engage_settings_shortcodes_display_published_dates" name="ql_engage_settings_shortcodes_display_published_dates" <?php echo $display_published_dates_checked ?> />
					<p class="description">Determines whether or not to show the Engage article published date in the embedded article HTML.</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ql_engage_settings_shortcodes_include_jquery">Include jQuery</label>
				</th>
				<td>
					<?php $include_jquery_checked = (get_option('ql_engage_settings_shortcodes_include_jquery') == 'on') ? 'checked' : '' ?>
					<input type="checkbox" id="ql_engage_settings_shortcodes_include_jquery" name="ql_engage_settings_shortcodes_include_jquery" <?php echo $include_jquery_checked ?> />
					<p class="description">Determines whether or not to include jQuery in the embedded article HTML. Check this if your theme does not use jQuery.</p>
				</td>
			</tr>
		</table>
		
		<a id="save-settings" href="#" class="button button-primary">Save</a>
	</form>
</div>
