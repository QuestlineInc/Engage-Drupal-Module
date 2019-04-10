(function($) {
	CKEDITOR.plugins.add('EngageArticleSearch', {
		icons: 'EngageArticleSearch',
		init: function(editor) {
			// Create the new command
			editor.addCommand('EngageArticleSearch', new CKEDITOR.dialogCommand('EngageArticleSearchDialog'));

			// Add the button
			editor.ui.addButton('EngageArticleSearch', {
				command : 'EngageArticleSearch',
				label: 'Engage Article Search',
				toolbar : 'insert',
				icon : this.path + 'images/engage-16.png'
			});
			
			// Load styles
			//editor.addContentsCss(this.path + 'css/simplePagination.css');
			
			// Load scripts
			//CKEDITOR.scriptLoader.load(this.path + 'js/jquery.simplePagination.js');
			//CKEDITOR.scriptLoader.load(this.path + 'js/jquery.blockUI.js');
			//CKEDITOR.scriptLoader.load(this.path + 'js/functions.js');
			
			// Then add the popup dialog box
			CKEDITOR.dialog.add('EngageArticleSearchDialog', function(editor) {
				return {
					title: 'Insert Article into Editor',
					width: 800,
					height: 600,
					resizable: CKEDITOR.DIALOG_RESIZE_NONE,
					contents: [
						{
							id: 'EngageArticleSearchContent',
							elements: [
								{
									id: 'search',
									type : 'html',
									html : engageArticleSearchContent(editor.plugins.EngageArticleSearch.path)
								}
							]
						}
					],
					onLoad: function() {
						/*
						$(document).ready(function() {
							function searchArticles(pageIndex) {
								$('#ql-engage-article-search-page-index').val(pageIndex);

								blockElement($('#ql-engage-article-search-form'), editor.plugins.EngageArticleSearch.path + 'images/loader.gif');
								
								$('#ql-engage-article-search-list-wrap').empty();
								$('#ql-engage-article-search-list-wrap').hide();
								$('#ql-engage-article-search-list-noresults').hide();
								
								var ajaxUrl = window.location.protocol + '//' + window.location.hostname + editor.plugins.EngageArticleSearch.path + 'EngageArticleSearchAjax.php';
								var formData = $('#ql-engage-article-search-form').serialize();
								
								$.ajax({
									type: 'POST',
									url: ajaxUrl,
									data: formData,
									dataType: 'json',
									success: function(list) {
										unblockElement($('#ql-engage-article-search-form'));
										
										if (list !== null) {
											console.log(list);
											
											var results = buildArticleListSummary(list.TotalResults);
											results += buildArticleListTable($(list.Articles));
											
											$('#ql-engage-article-search-list-wrap').append(results);
											$('#ql-engage-article-search-list-wrap').show();
											$('#ql-engage-article-search-list-noresults').hide();
											setupPagination(list.TotalResults);
										}
										else {
											$('#ql-engage-article-search-list-wrap').hide();
											$('#ql-engage-article-search-list-noresults').show();
										}
									}
								});
							};
							
							function buildArticleListSummary(totalResults) {
								var pageIndex = parseInt($('#ql-engage-article-search-page-index').val());
								var pageSize = parseInt($('#ql-engage-article-search-page-size').val());
								
								var startPos = parseInt(pageIndex * pageSize);
								var endPos = Math.min(pageSize, totalResults - (pageIndex * pageSize));
								
								var rangeStart = parseInt(startPos + 1);
								var rangeEnd = parseInt(startPos + endPos);
								
								var summary = '';
								summary += '<div style="margin-bottom: 5px;">';
								summary += '	<div style="float: left; font-style: italic; padding-top: 3px;">';
								summary += '		Showing ' + rangeStart + '-' + rangeEnd + ' of ' + totalResults + ' results';
								summary += '	</div>';
								summary += '	<div id="ql-engage-article-search-list-pagination" style="float: right;"></div>';
								summary += '	<div style="clear: both;"></div>';
								summary += '</div>';
								
								return summary;
							};
							
							function buildArticleListTable($articles) {
								var list = '';
								list += '<div style="padding: 0px 20px 0px 20px; background-color: #ffffff; border: solid 1px #e5e5e5; border-radius: 3px;">';
								list += '<table cellpadding="0" cellspacing="0" style="width: 100%;">';
								
								var counter = 1;
								$articles.each(function(index, article) {
									var articleImageStyle = 'vertical-align: top; padding: 20px 0px 20px 0px; border-top: solid 1px #e5e5e5; width: 80px;';
									var articleTextStyle = 'vertical-align: top; padding: 20px 0px 20px 10px; border-top: solid 1px #e5e5e5;';
									var articleActionsStyle = 'vertical-align: top; padding: 20px 0px 20px 10px; border-top: solid 1px #e5e5e5;';
									
									if (counter == 1) {
										articleImageStyle += ' border-top: none;';
										articleTextStyle += ' border-top: none;';
										articleActionsStyle += ' border-top: none;';
									}
									
									list += '<tr>';
									list += '	<td style="' + articleImageStyle + '"><img src="' + article.ThumbnailImage + '" alt="Thumnbail" style="width: 80px; height: 60px; border-radius: 3px;" /></td>';
									list += '	<td style="' + articleTextStyle + '">';
									list += '		<h4 style="margin-top: 0px; margin-bottom: 5px;">' + article.Title + '</h4>';
									list += '		<div style="white-space: normal;">' + article.Summary + '</div>';
									list += '	</td>';
									list += '	<td style="' + articleActionsStyle + '">';
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
								var currentPage = parseInt($('#ql-engage-article-search-page-index').val()) + 1;
								
								$('#ql-engage-article-search-list-pagination').pagination({
									currentPage: currentPage,
									items: totalResults,
									itemsOnPage: $('#ql-engage-article-search-page-size').val(),
									cssStyle: 'light-theme',
									prevText: '&laquo;',
									nextText: '&raquo;',
									onPageClick: function(pageNumber) {
										var pageIndex = parseInt(pageNumber - 1);
										searchArticles(pageIndex);
									}
								});
							};
							
							$('#ql-engage-article-search').click(function() {
								searchArticles(0);			
								return false;
							});
							
							$('#ql-engage-article-search-form').submit(function() {
								searchArticles(0);			
								return false;
							});
						});
						*/
					},
					/*
					onShow: function() {
						//blockElement($('#ql-engage-article-search-form'), editor.plugins.EngageArticleSearch.path + 'images/loader.gif');
					},
					*/
					onOk: function() {
						var content = '[ql_engage_article id="" type="" /]';
						editor.insertHtml(content);
					}
				};
			});
		}
	});
	
	function engageArticleSearchContent(pluginPath) {
		//var engageIcon = window.location.protocol + '//' + window.location.hostname + pluginPath + 'images/engage-32.png';
		//var searchIcon = window.location.protocol + '//' + window.location.hostname + pluginPath + 'images/search.png';
		
		var iframeSrc = window.location.protocol + '//' + window.location.hostname + pluginPath + 'EngageArticleSearchDialog.php';
		
		var html = '';
		html += '<div style="width: 100%; height: 600px;">';
		html += '	<iframe src="' + iframeSrc + '" style="width: 100%; height: 100%;"></iframe>';
		html += '</div>';
		
		/*
		var html = '';

		// Inline style block
		html += '<style type="text/css">';
		html += '	.ql-engage-article-search-button {';
		html += '		cursor: pointer !important;';
		html += '		padding: 4px 1.5em !important;';
		html += '		border: 1px solid #a6a6a6 !important;';
		html += '		border-radius: 20em !important;';
		html += '		border-color: #1e5c90 !important;';
		html += '		background-color: #0071b8 !important;';
		html += '		background-image: -webkit-linear-gradient(top,#007bc6,#0071b8) !important;';
		html += '		background-image: linear-gradient(to bottom,#007bc6,#0071b8) !important;';
		html += '		text-align: center !important;';
		html += '		text-decoration: none !important;';
		html += '		text-shadow: 0 1px hsla(0,0%,0%,0.5) !important;';
		html += '		font-size: 13px !important;';
		html += '		font-weight: 700 !important;';
		html += '		color: #ffffff !important;';
		html += '		-webkit-font-smoothing: antialiased !important;';
		html += '		-webkit-transition: all 0.1s !important;';
		html += '		transition: all 0.1s !important;';
		html += '	}';
		html += '	.ql-engage-article-search-button:hover {';
		html += '		background-color: #2369a6 !important;';
		html += '		background-image: -webkit-linear-gradient(top,#0c97ed,#1f86c7) !important;';
		html += '		background-image: linear-gradient(to bottom,#0c97ed,#1f86c7) !important;';
		html += '	}';
		html += '	.light-theme.simple-pagination a,';
		html += '	.light-theme.simple-pagination span {';
		html += '		text-decoration: none !important;';
		html += '		font-size: 10px !important;';
		html += '		padding: 0 2px !important;';
		html += '		line-height: 18px !important;';
		html += '	}';
		html += '</style>';
		
		// Icon and header text
		html += '<div style="margin-left: 10px;">';
		html += '	<div style="display: inline-block; vertical-align: middle;">';
		html += '		<img src="' + engageIcon + '" alt="" />';
		html += '	</div>';
		html += '	<div style="display: inline-block; vertical-align: middle; margin-left: 10px; font-size: 1.5em;">';
		html += '		Engage';
		html += '	</div>';
		html += '</div>';

		// Search form
		html += '<form id="ql-engage-article-search-form">';
		html += '	<input type="hidden" id="ql-engage-article-search-page-index" name="ql-engage-article-search-page-index" value="0" />';
		html += '	<input type="hidden" id="ql-engage-article-search-page-size" name="ql-engage-article-search-page-size" value="10" />';
		html += '	<div style="margin-left: 10px; margin-top: 20px;">';
		html += '		<div style="display: inline-block; vertical-align: middle;">';
		html += '			<img src="' + searchIcon + '" alt="" />';
		html += '		</div>';
		html += '		<div style="display: inline-block; vertical-align: middle; margin-left: 10px;">';
		html += '			<input type="text" style="width: 350px;" id="ql-engage-article-search-keyword" name="ql-engage-article-search-keyword" placeholder="Search Articles" />';
		html += '		</div>';
		html += '		<div style="display: inline-block; vertical-align: middle; margin-left: 10px;">';
		html += '			<a id="ql-engage-article-search" href="#" class="ql-engage-article-search-button">Search</a>';
		html += '		</div>';
		html += '	</div>';
		html += '	<div id="ql-engage-article-search-list-wrap" style="display: none; margin-top: 20px;"></div>';
		html += '	<div id="ql-engage-article-search-list-noresults" style="display: none; margin-top: 20px;">';
		html += '		<p>There are no articles that matched your search criteria.</p>';
		html += '	</div>';
		html += '</form>';
		*/
		
		return html;
	}
})(jQuery);
