<?php
class QuestlineEngageApi {
	private $_api_key;
	
	public function __construct() {
		$this->_api_key = get_option('ql_engage_settings_apikey');
		
		// Ajax hook for searching articles
		add_action('wp_ajax_search_engage_articles', array($this, 'search_engage_articles'));
		add_action('wp_ajax_nopriv_search_engage_articles', array($this, 'search_engage_articles'));
	}
	
	public function get_article_preview($article_id = '', $article_type = '') {
		$article = '';
		
		if ($article_id != '' && $article_type != '') {
			$url = QL_ENGAGE_API_URL . '/content/articles/' . $article_type . '/' . $article_id . '?format=html';
			
			$args = array(
				'headers' => array('Authorization' => 'Basic ' . base64_encode($this->_api_key)),
				'timeout' => 60
			);
			
			$response = wp_remote_get(esc_url_raw($url), $args);
			$response_code = wp_remote_retrieve_response_code($response);
			
			if ($response_code == '200') {
				$response_body = wp_remote_retrieve_body($response);
				$article = json_decode($response_body)->Article;
			}
		}
		
		return $article;
	}
	
	public function get_article_embed($article_id = '', $article_type = '') {
		$embed = '';
		
		if ($article_id != '' && $article_type != '') {
			$url = QL_ENGAGE_API_URL . '/content/articles/' . $article_type . '/' . $article_id . '?expand=embed';
			
			$args = array(
				'headers' => array('Authorization' => 'Basic ' . base64_encode($this->_api_key)),
				'timeout' => 60
			);
			
			$response = wp_remote_get(esc_url_raw($url), $args);
			$response_code = wp_remote_retrieve_response_code($response);
			
			if ($response_code == '200') {
				$response_body = wp_remote_retrieve_body($response);
				$embed = json_decode($response_body)->Article->Embed;
			}
		}
		
		return $embed;
	}
	
	public function search_engage_articles() {
		$results = null;

		$valid_nonce = check_ajax_referer('ql_engage_search_articles_form');
		
		if (isset($_POST) && $valid_nonce) {
			$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
			$page_index = isset($_POST['page_index']) ? $_POST['page_index'] : '0';
			$page_size = isset($_POST['page_size']) ? $_POST['page_size'] : '10';
			
			$url = QL_ENGAGE_API_URL . '/content/articles?search=' . urlencode($keyword) . '&page=index~' . $page_index . ',size~' . $page_size;
			
			$args = array(
				'headers' => array('Authorization' => 'Basic ' . base64_encode($this->_api_key)),
				'timeout' => 60
			);
			
			$response = wp_remote_get(esc_url_raw($url), $args);
			$response_code = wp_remote_retrieve_response_code($response);
			
			if ($response_code == '200') {
				$response_body = wp_remote_retrieve_body($response);

				$list = json_decode($response_body);
				$results = array();
				$results['Articles'] = $list->Articles;
				$results['PageIndex'] = $list->PageIndex;
				$results['PageSize'] = $list->PageSize;
				$results['TotalPages'] = $list->TotalPages;
				$results['TotalResults'] = $list->TotalResults;
			}
		}
		
		echo json_encode($results);
		die();
	}
}
?>