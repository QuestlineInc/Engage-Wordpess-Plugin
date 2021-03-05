<?php
// Require wp_blog_header so that we can use WP objects
$file = dirname(__FILE__);
$real_path = realpath($file . '/./');
$file_path = explode('wp-content', $real_path);
define('WP_USE_THEMES', false);
require($file_path[0] . '/wp-blog-header.php');

global $ql_engage;

$article_id = $_GET['id'];
$article_type = $_GET['type'];

$article = $ql_engage->api->get_article_preview($article_id, $article_type);
?>

<html>

<head>
	<?php if ($article != null) { ?>
		<title>Article Preview - <?php echo $article->Title ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo QL_ENGAGE_PLUGIN_URL ?>/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo QL_ENGAGE_PLUGIN_URL ?>/css/preview-article.css">
		<script type="text/javascript" src="<?php echo QL_ENGAGE_PLUGIN_URL ?>/js/jquery-3.3.1.min.js"></script>
		<?php echo $article->Head ?>
	<?php } else { ?>
		<title>Article Not Found Or Is Not Supported</title>
	<?php } ?>
</head>

<body>
	<?php if ($article != null) { ?>
		<div class="container article-wrap">
			<div class="row">
				<div class="col-8">
					<div class="article-label">Title</div>
					<div class="article-value"><h1><?php echo $article->Title ?></h1></div>

					<div class="article-label">Summary</div>
					<div class="article-value"><?php echo $article->Summary ?></div>

					<div class="article-label">Body</div>
					<div class="article-value body"><?php echo $article->Body ?></div>
				</div>
				<div class="col-4">
					<div class="article-meta">
						<div class="article-label">Added to Library</div>
						<div class="article-value"><?php echo date('l, F j, Y', strtotime($article->Published)) ?></div>
						
						<div class="article-label">Article ID</div>
						<div class="article-value articleid"><?php echo $article->ArticleId ?></div>
					</div>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<p>Article not found or is not supported.</p>
	<?php } ?>
</body>

</html>
