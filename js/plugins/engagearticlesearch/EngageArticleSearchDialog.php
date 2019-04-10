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
		
		function insertArticleShortcode(articleId, articleType) {
			window.send_to_editor('[ql_engage_article id="' + articleId + '" type="' + articleType + '" /]');
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
							console.log(list);
							
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
					list += '	<td class="' + articleImageStyle + '">';
					list += '		<img src="' + article.ThumbnailImage + '" alt="Thumbnail" style="width: 80px; height: 60px; border-radius: 3px;" />';
					list += '	</td>';
					list += '	<td class="' + articleTextStyle + '">';
					list += '		<h4>' + article.Title + '</h4>';
					list += '		<span>' + article.Summary + '</span>';
					list += '	</td>';
					list += '	<td class="' + articleActionsStyle + '">';
					list += '		<a href="#" onclick="javascript:insertArticleShortcode(\'' + article.ArticleId + '\', \'' + article.Type + '\')">Insert</a>';
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
