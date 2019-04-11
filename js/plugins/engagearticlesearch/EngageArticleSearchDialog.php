<html>

<head>
	<title>Engage Article Search Dialog</title>
	<link rel="stylesheet" type="text/css" href="css/simplePagination.css">
	<link rel="stylesheet" type="text/css" href="css/article-search.css">
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/jquery.simplePagination.js"></script>
	<script type="text/javascript" src="js/jquery.blockUI.js"></script>
	<script type="text/javascript" src="js/functions.js"></script>
	<script type="text/javascript">
		function previewArticle(articleId, articleType) {
			var previewUrl = 'EngageArticlePreview.php?id=' + articleId + '&type=' + articleType;
			window.open(previewUrl, '_blank');
		};
		
		function selectArticle(articleId, articleType) {
			$('#selected_article_id').val(articleId);
			$('#selected_article_type').val(articleType);

			// Clear any existing selection
			$('.search_list_table td[data-article-thumb]').css('background-color', '#ffffff');
			$('.search_list_table td[data-article-text]').css('background-color', '#ffffff');
			$('.search_list_table td[data-article-actions]').css('background-color', '#ffffff');
			
			// Highlight selected row
			$('.search_list_table td[data-article-thumb="' + articleId + '"]').css('background-color', '#d0e9f9');
			$('.search_list_table td[data-article-text="' + articleId + '"]').css('background-color', '#d0e9f9');
			$('.search_list_table td[data-article-actions="' + articleId + '"]').css('background-color', '#d0e9f9');
			
			return false;
		};
		
		$(document).ready(function() {
			function searchArticles(pageIndex) {
				$('#page_index').val(pageIndex);

				blockElement($('.search_form'), 'images/loader.gif');
				
				$('.search_list_wrap').empty();
				$('.search_list_wrap').hide();
				$('.search_list_noresults').hide();
				
				var formData = $('.search_form').serialize();
				
				$.ajax({
					type: 'POST',
					url: 'EngageArticleSearchAjax.php',
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
					var articleActionsStyle = 'search_list_actions';
					
					if (counter == 1) {
						articleImageStyle += ' notop';
						articleTextStyle += ' notop';
						articleActionsStyle += ' notop';
					}
					
					list += '<tr>';
					list += '	<td class="' + articleImageStyle + '" data-article-thumb="' + article.ArticleId + '">';
					list += '		<img src="' + article.ThumbnailImage + '" alt="Thumbnail" />';
					list += '	</td>';
					list += '	<td class="' + articleTextStyle + '" data-article-text="' + article.ArticleId + '">';
					list += '		<h4>' + article.Title + '</h4>';
					list += '		<span>' + article.Summary + '</span>';
					list += '	</td>';
					list += '	<td class="' + articleActionsStyle + '" data-article-actions="' + article.ArticleId + '">';
					list += '		<a href="#" onclick="javascript:selectArticle(\'' + article.ArticleId + '\', \'' + article.Type + '\')">Select</a>';
					list += '		<br /><br />';
					list += '		<a href="#" onclick="javascript:previewArticle(\'' + article.ArticleId + '\', \'' + article.Type + '\')">Preview</a>';
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
		});
	</script>
</head>

<body>
	<form class="search_form">
		<input type="hidden" id="page_index" name="page_index" value="0" />
		<input type="hidden" id="page_size" name="page_size" value="10" />
		<input type="hidden" id="selected_article_id" name="selected_article_id" />
		<input type="hidden" id="selected_article_type" name="selected_article_type" />
		
		<div class="search_header">
			<div class="search_icon">
				<img src="images/engage-32.png" alt="Icon" />
			</div>	
			<div class="search_box">
				<input type="text" id="search_keyword" name="search_keyword" placeholder="Search Engage Articles" />
			</div>
			<div class="search_button">
				<a href="#">Search</a>
			</div>
		</div>
	</form>
	
	<div class="search_list_wrap"></div>
	
	<div class="search_list_noresults">
		<p>There are no articles that matched your search criteria.</p>
	</div>
</body>

</html>
