<?php
class QuestlineEngageShortcode {
	public function __construct() {
		add_shortcode('ql_engage_article', array($this, 'do_shortcode'));
	}

	public function do_shortcode($atts) {
		global $ql_engage;
		
		$output = '';
		
		extract(shortcode_atts(array(
			'id' => '',
			'type' => '',
			'display_title' => '',
			'display_published_date' => '',
			'include_jquery' => ''
		), $atts));
		
		$article_id = sanitize_text_field("{$id}");
		$article_type = sanitize_text_field("{$type}");
		$display_title = sanitize_text_field("{$display_title}");
		$display_published_date = sanitize_text_field("{$display_published_date}");
		$include_jquery = sanitize_text_field("{$include_jquery}");

		if ($article_id != '' && $article_type != '') {
			// Check to include jquery
			$output .= $this->include_jquery($include_jquery);
			
			// Call out to Engage API to retrieve article	
			$output .= $ql_engage->api->get_article_embed($article_id, $article_type);
			
			// Once we have the article embed code, add additional css
			// to hide article title and/or published date, if defined
			$output .= $this->hide_title_and_or_published_date($article_id, $display_title, $display_published_date);
		}
		
		return $output;
	}
	
	private function include_jquery($include_jquery) {
		$settings_include_jquery = get_option('ql_engage_settings_shortcodes_include_jquery');
		$jquery_src = QL_ENGAGE_PLUGIN_URL . '/js/jquery-3.3.1.min.js';
		$jquery_script = '<script type="text/javascript" src="' . $jquery_src . '"></script>';
		
		$output = '';
		
		// Check to add jquery to the output. If the include_jquery param was given
		// in the shortcode, use it; otherwise, use the filter setting
		if ($include_jquery != '') {
			if ($include_jquery == 'true') {
				$output = $jquery_script;
			}
		}
		else {
			if ($settings_include_jquery == 'on') {
				$output = $jquery_script;
			}
		}
		
		return $output;
	}
	
	private function hide_title_and_or_published_date($article_id, $display_title, $display_published_date) {
		$settings_display_titles = get_option('ql_engage_settings_shortcodes_display_titles');
		$settings_display_published_dates = get_option('ql_engage_settings_shortcodes_display_published_dates');
		
		$css_hide_title = '#ql-embed-' . $article_id . ' h1.ql-embed-article__title { display: none; }';
		$css_hide_published_date = '#ql-embed-' . $article_id . ' p.ql-embed-article__pubdate { display: none; }';
		
		$css = '<style type="text/css">';
		
		// Check to hide article title. If the display_title param was given
		// in the shortcode, use it; otherwise, use the settings value
		if ($display_title != '') {
			if ($display_title == 'false') {
				$css .= $css_hide_title;
			}
		}
		else {
			if ($settings_display_titles != 'on') {
				$css .= $css_hide_title;
			}
		}
		
		// Check to hide article published date. If the display_published_date param
		// was given in the shortcode, use it; otherwise, use the settings value
		if ($display_published_date != '') {
			if ($display_published_date == 'false') {
				$css .= $css_hide_published_date;
			}
		}
		else {
			if ($settings_display_published_dates != 'on') {
				$css .= $css_hide_published_date;
			}
		}
		
		$css .= '</style>';
		
		return $css;
	}
}
?>
