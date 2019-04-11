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
			
			// Add the dialog box
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
						// Nothing to see... move along, move along...
					},
					onShow: function() {
						// Nothing to see... move along, move along...
					},
					onOk: function() {						
						var articleId = $('#ql_engage_article_search_iframe').contents().find('#selected_article_id').val();
						var articleType = $('#ql_engage_article_search_iframe').contents().find('#selected_article_type').val();
						var shortcode = '[ql_engage_article id="' + articleId + '" type="' + articleType + '" /]';
						editor.insertHtml(shortcode);
					}
				};
			});
		}
	});
	
	function engageArticleSearchContent(pluginPath) {
		var iframeSrc = window.location.protocol + '//' + window.location.hostname + pluginPath + 'EngageArticleSearchDialog.php';
		
		var html = '';
		html += '<div style="width: 100%; height: 600px;">';
		html += '	<iframe id="ql_engage_article_search_iframe" src="' + iframeSrc + '" style="width: 100%; height: 100%;"></iframe>';
		html += '</div>';

		return html;
	}
})(jQuery);
