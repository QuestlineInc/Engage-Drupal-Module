<?php
require_once '..\..\..\core\EngageDrupalBootstrap.php';
require_once '..\..\..\core\EngageCommon.php';
require_once '..\..\..\core\EngageApi.php';

$article_id = $_GET['id'];
$article_type = $_GET['type'];

$api = new \Drupal\questline_engage\Core\EngageApi();
$article = $api->getArticlePreview($article_id, $article_type);
?>

<html>

<head>
	<?php if ($article != null) { ?>
		<title>Engage Article Preview - <?php echo $article->Title ?></title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/article-preview.css">
		<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
		<?php echo $article->Head ?>
	<?php } else { ?>
		<title>Engage Article Not Found Or Is Not Supported</title>
	<?php } ?>
</head>

<body>
	<?php if ($article != null) { ?>
		<div class="container article_wrap">
			<div class="row">
				<div class="col-8">
					<div class="article_label">Title</div>
					<div class="article_value"><h1><?php echo $article->Title ?></h1></div>

					<div class="article_label">Summary</div>
					<div class="article_value"><?php echo $article->Summary ?></div>

					<div class="article_label">Body</div>
					<div class="article_value body"><?php echo $article->Body ?></div>
				</div>
				<div class="col-4">
					<div class="article_meta">
						<div class="article_label">Added to Library</div>
						<div class="article_value"><?php echo date('l, F j, Y', strtotime($article->Published)) ?></div>
						
						<div class="article_label">Article ID</div>
						<div class="article_value articleid"><?php echo $article->ArticleId ?></div>
					</div>
				</div>
			</div>
		</div>
	<?php } else { ?>
		<p>Article not found or is not supported.</p>
	<?php } ?>
</body>

</html>
