<?php
/*
Plugin Name: Questline Engage
Plugin URI: http://www.questline.com
Description: The official WordPress plugin for Questline's Engage platform.
Version: 0.1.0
Author: Questline, Inc.
Author URI: http://www.questline.com
*/

class QuestlineEngage {
	public $api;
	public $common;
	public $shortcode;
	
	public function __construct() {
		// Do initial setup
		$this->define_global_constants();
		$this->register_activation_hooks();
		$this->setup_hooks();
		
		// Pull in other necessary files
		require_once 'config\config.php';
		require_once 'api.php';
		require_once 'common.php';
		require_once 'shortcode.php';
		
		// Instantiate properties
		$this->api = new QuestlineEngageApi();
		$this->common = new QuestlineEngageCommon();
		$this->shortcode = new QuestlineEngageShortcode();
	}

	public function define_global_constants() {
		define('QL_ENGAGE_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
		define('QL_ENGAGE_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . QL_ENGAGE_PLUGIN_NAME);
		define('QL_ENGAGE_PLUGIN_URL', WP_PLUGIN_URL . '/' . QL_ENGAGE_PLUGIN_NAME);
		define('QL_ENGAGE_PLUGIN_VERSION_KEY', 'ql_engage_version');
		define('QL_ENGAGE_PLUGIN_VERSION_NUM', '0.1.0');
	}
	
	public function register_activation_hooks() {
		register_activation_hook(__FILE__, array($this, 'do_activation'));
		register_deactivation_hook(__FILE__, array($this, 'do_deactivation'));
	}

	public function setup_hooks() {
		add_action('admin_footer', array($this, 'media_button_admin_footer'));
		add_action('admin_menu', array($this, 'add_admin_menu'));
		add_action('media_buttons_context', array($this, 'media_button'));
		
		// Ajax hook for saving the API key
		add_action('wp_ajax_save_engage_settings_apikey', array($this, 'save_engage_settings_apikey'));
		add_action('wp_ajax_nopriv_save_engage_settings_apikey', array($this, 'save_engage_settings_apikey'));
		
		// Ajax hook for saving the shortcode settings
		add_action('wp_ajax_save_engage_settings_shortcodes', array($this, 'save_engage_settings_shortcodes'));
		add_action('wp_ajax_nopriv_save_engage_settings_shortcodes', array($this, 'save_engage_settings_shortcodes'));
	}
	
	public function activate() {
		update_option(QL_ENGAGE_PLUGIN_VERSION_KEY, QL_ENGAGE_PLUGIN_VERSION_NUM);
	}
	
	public function deactivate() {
		delete_option(QL_ENGAGE_PLUGIN_VERSION_KEY);
	}
	
	public function do_activation($network_wide) {
		if ($network_wide) {
			$this->call_function_for_each_site(array($this, 'activate'));
		}
		else {
			$this->activate();
		}
	}
	
	public function do_deactivation($network_wide) {	
		if ($network_wide) {
			$this->call_function_for_each_site(array($this, 'deactivate'));
		}
		else {
			$this->deactivate();
		}
	}
	
	public function call_function_for_each_site($function) {
		global $wpdb;
		
		// Hold this so we can switch back to it
		$current_blog = $wpdb->blogid;
		
		// Get all the blogs/sites in the network and invoke the function for each one
		$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		foreach ($blog_ids as $blog_id) {
			switch_to_blog($blog_id);
			call_user_func($function);
		}
		
		// Now switch back to the root blog
		switch_to_blog($current_blog);
	}
	
	public function add_admin_menu() {
		// First add the top-level menu page, which simply points to the API key settings page
		$page_title = 'Questline Engage - Settings - API Key';
		$menu_title = 'Engage';
		$capability = 'manage_options';
		$menu_slug = 'ql-engage-settings-apikey';
		$function = array($this, 'add_admin_menu_settings_apikey');
		$icon_url = QL_ENGAGE_PLUGIN_URL . '/images/engage-16.png';
		$position = 1001; // Should place it at the bottom or near the bottom in the menu
		add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);

		// Then add the API key settings page itself
		$parent_slug = 'ql-engage-settings-apikey';
		$page_title = 'Questline Engage - Settings - API Key';
		$menu_title = 'API Key';
		$capability = 'manage_options';
		$menu_slug = 'ql-engage-settings-apikey';
		$function = array($this, 'add_admin_menu_settings_apikey');
		add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
		
		// Then add the shortcodes settings page
		$parent_slug = 'ql-engage-settings-apikey';
		$page_title = 'Questline Engage - Settings - Shortcodes';
		$menu_title = 'Shortcodes';
		$capability = 'manage_options';
		$menu_slug = 'ql-engage-settings-shortcodes';
		$function = array($this, 'add_admin_menu_settings_shortcodes');
		add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
	}
	
	public function add_admin_menu_settings_apikey() {
		require_once 'settings-apikey.php';
	}

	public function add_admin_menu_settings_shortcodes() {
		require_once 'settings-shortcodes.php';
	}
	
	public function media_button($context) {
		global $pagenow;
		$output = '';

		// Only run in post/page creation and edit screens
		if (in_array($pagenow, array('post.php', 'page.php', 'post-new.php', 'post-edit.php'))) {
			$button_text = 'Engage';
			$modal_title = 'Insert Article into Editor';
			$icon = QL_ENGAGE_PLUGIN_URL . '/images/engage-16.png';
			$img = '<span class="wp-media-buttons-icon" style="background-image: url(' . $icon . '); width: 16px; height: 16px; margin-top: 1px;"></span>';
			$output = '<a href="#TB_inline?width=800&inlineId=ql_engage_article_search" class="thickbox button" title="' . $modal_title . '" style="padding-left: .4em;">' . $img . ' ' . $button_text . '</a>';
		}

		return $context . $output;
	}
	
	public function media_button_admin_footer() {
		require_once 'media-button.php';
	}
	
	public function save_engage_settings_apikey() {
		$valid_nonce = check_ajax_referer('ql_engage_settings_apikey_form');
		
		if (isset($_POST) && $valid_nonce) {
			$api_key = isset($_POST['ql_engage_settings_apikey']) ? $_POST['ql_engage_settings_apikey'] : '';
			update_option('ql_engage_settings_apikey', $api_key);

			echo 'success';
			die();
		}
	}
	
	public function save_engage_settings_shortcodes() {
		$valid_nonce = check_ajax_referer('ql_engage_settings_shortcodes_form');
		
		if (isset($_POST) && $valid_nonce) {
			$display_titles = isset($_POST['ql_engage_settings_shortcodes_display_titles']) ? $_POST['ql_engage_settings_shortcodes_display_titles'] : '';
			$display_published_dates = isset($_POST['ql_engage_settings_shortcodes_display_published_dates']) ? $_POST['ql_engage_settings_shortcodes_display_published_dates'] : '';
			$include_jquery = isset($_POST['ql_engage_settings_shortcodes_include_jquery']) ? $_POST['ql_engage_settings_shortcodes_include_jquery'] : '';
			
			update_option('ql_engage_settings_shortcodes_display_titles', $display_titles);
			update_option('ql_engage_settings_shortcodes_display_published_dates', $display_published_dates);
			update_option('ql_engage_settings_shortcodes_include_jquery', $include_jquery);

			echo 'success';
			die();
		}
	}
}

// Let's get this party started
$ql_engage = new QuestlineEngage();
?>