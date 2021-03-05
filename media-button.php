<?php
global $pagenow;
?>

<?php // Only run in post/page creation and edit screens ?>
<?php if (in_array($pagenow, array('post.php', 'page.php', 'post-new.php', 'post-edit.php'))) { ?>
	<link type="text/css" rel="stylesheet" href="<?php echo QL_ENGAGE_PLUGIN_URL ?>/css/simplePagination.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo QL_ENGAGE_PLUGIN_URL ?>/css/media-button.css" />
	<script type="text/javascript" src="<?php echo QL_ENGAGE_PLUGIN_URL ?>/js/jquery.blockUI.js"></script>
	<script type="text/javascript" src="<?php echo QL_ENGAGE_PLUGIN_URL ?>/js/jquery.simplePagination.js"></script>
	<script type="text/javascript" src="<?php echo QL_ENGAGE_PLUGIN_URL ?>/js/functions.js"></script>
	<script type="text/javascript">
		function previewArticle(articleId, articleType) {
			var previewUrl = '<?php echo QL_ENGAGE_PLUGIN_URL ?>' + '/preview-article.php?id=' + articleId + '&type=' + articleType;
			window.open(previewUrl, '_blank');
		};
		
		function insertArticleShortcode(articleId, articleType) {
			window.send_to_editor('[ql_engage_article id="' + articleId + '" type="' + articleType + '" /]');
		};
		
		jQuery(document).ready(function($) {			
			function searchArticles(pageIndex) {
				$('#page_index').val(pageIndex);

				blockElement($('.search_form'), '<?php echo QL_ENGAGE_PLUGIN_URL ?>' + '/images/loader.gif');
				
				$('.search_list_wrap').empty();
				$('.search_list_wrap').hide();
				$('.search_list_noresults').hide();
				
				var formData = $('.search_form').serialize();
				formData += '&action=search_engage_articles';
				
				$.ajax({
					type: 'POST',
					url: '<?php echo admin_url('admin-ajax.php') ?>',
					data: formData,
					dataType: 'json',
					success: function(list) {
						unblockElement($('.search_form'));
						
						if (list !== null) {
							var results = buildArticleListSummary(list.TotalResults);
							results += buildArticleListTable($(list.Articles));
							
							$('.search_list_wrap').append(results);
							$('.search_list_wrap').show();
							$('.search_list_noresults').hide();
							setupPagination(list.TotalResults);
						}
						else {
							$('.search_list_wrap').hide();
							$('.search_list_noresults').show();
						}
					}
				});
			};
			
			function buildArticleListSummary(totalResults) {
				var pageIndex = parseInt($('#page_index').val());
				var pageSize = parseInt($('#page_size').val());
				
				var startPos = parseInt(pageIndex * pageSize);
				var endPos = Math.min(pageSize, totalResults - (pageIndex * pageSize));
				
				var rangeStart = parseInt(startPos + 1);
				var rangeEnd = parseInt(startPos + endPos);
				
				var summary = '';
				summary += '<div class="search_list_summary">';
				summary += '	<div class="search_list_range">';
				summary += '		Showing ' + rangeStart + '-' + rangeEnd + ' of ' + totalResults + ' results';
				summary += '	</div>';
				summary += '	<div class="search_list_pagination"></div>';
				summary += '	<div class="clear"></div>';
				summary += '</div>';
				
				return summary;
			};
			
			function buildArticleListTable($articles) {			
				var list = '';
				list += '<div class="search_list_table">';
				list += '<table cellpadding="0" cellspacing="0">';
				
				var counter = 1;
				$articles.each(function(index, article) {
					var articleImageStyle = 'search_list_thumb';
					var articleTextStyle = 'search_list_text';
					
					if (counter == 1) {
						articleImageStyle += ' notop';
						articleTextStyle += ' notop';
					}
					
					list += '<tr>';
					list += '	<td class="' + articleImageStyle + '">';
					list += '		<img src="' + article.ThumbnailImage + '" alt="Thumbnail" />';
					list += '	</td>';
					list += '	<td class="' + articleTextStyle + '">';
					list += '		<h4>' + article.Title + '</h4>';
					list += '		<div>' + article.Summary + '</div>';
					list += '		<div class="search_list_actions">';
					list += '			<a href="#" onclick="javascript:insertArticleShortcode(\'' + article.ArticleId + '\', \'' + article.Type + '\')">Insert</a>';
					list += '			|';
					list += '			<a href="#" onclick="javascript:previewArticle(\'' + article.ArticleId + '\', \'' + article.Type + '\')">Preview</a>';
					list += '		</div>';
					list += '	</td>';
					list += '</tr>';
					
					counter++;
				});
				
				list += '</table>';
				list += '</div>';
			
				return list;
			};
			
			function setupPagination(totalResults) {
				var currentPage = parseInt($('#page_index').val()) + 1;
				
				$('.search_list_pagination').pagination({
					currentPage: currentPage,
					items: totalResults,
					itemsOnPage: $('#page_size').val(),
					cssStyle: 'light-theme',
					prevText: '&laquo;',
					nextText: '&raquo;',
					onPageClick: function(pageNumber) {
						var pageIndex = parseInt(pageNumber - 1);
						searchArticles(pageIndex);
					}
				});
			};
			
			$('.search_button a').click(function() {
				searchArticles(0);			
				return false;
			});
			
			$('.search_form').submit(function() {
				searchArticles(0);			
				return false;
			});
			
			$('.search_list_wrap').on('mouseenter', '.search_list_text', function(e) {
				$(this).find('div:last').css('left', '0');
			});
			
			$('.search_list_wrap').on('mouseleave', '.search_list_text', function(e) {
				$(this).find('div:last').css('left', '-9999em');
			});
		});
	</script>
	
	<div id="ql_engage_article_search">
		<form class="search_form">
			<?php wp_nonce_field('ql_engage_search_articles_form') ?>
			<input type="hidden" id="page_index" name="page_index" value="0" />
			<input type="hidden" id="page_size" name="page_size" value="10" />
			
			<div class="search_header">
				<div class="search_icon">
					<img src="<?php echo QL_ENGAGE_PLUGIN_URL ?>/images/engage-32.png" alt="Icon" />
				</div>	
				<div class="search_box">
					<input type="text" id="keyword" name="keyword" placeholder="Search Engage Articles" />
				</div>
				<div class="search_button">
					<a href="#" class="button button-primary">Search</a>
				</div>
			</div>
		</form>
		
		<div class="search_list_wrap"></div>
		
		<div class="search_list_noresults">
			<p>There are no articles that matched your search criteria.</p>
		</div>
	</div>
<?php } ?>