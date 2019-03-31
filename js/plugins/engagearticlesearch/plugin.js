(function() {
	CKEDITOR.plugins.add('EngageArticleSearch', {
		icons: 'EngageArticleSearch',
		init: function(editor) {
			// Create the new command
			editor.addCommand('EngageArticleSearch', new CKEDITOR.dialogCommand('EngageArticleSearchDialog'));

			// Then add the button
			editor.ui.addButton('EngageArticleSearch', {
				command : 'EngageArticleSearch',
				label: 'Engage Article Search',
				toolbar : 'insert',
				icon : this.path + 'images/engage-16.png'
			});
			
			// Then add the popup dialog box
			CKEDITOR.dialog.add('EngageArticleSearchDialog', function(editor) {
				return {
					title: 'Insert Article into Editor',
					minWidth : 800,
					minHeight : 600,
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
					onShow: function() {
						//for (var i = 0; i < disabled.length; i++) {
						//	this.getContentElement('ArticleSearchPlugin', disabled[i]).disable();
						//}
					},
					onOk: function() {
						var content = '[ql_engage_article id="" type="" /]';
						editor.insertHtml(content);
					}
				};
			});
		}
	});
	
	function engageArticleSearchContent(pluginPath) {
		var img = 'http://' + window.location.hostname + pluginPath + 'images/engage-32.png';
		console.log(img);
		
		var html = '';
		html += '<div style="margin-left: 10px;">';
		html += '	<h2 style="padding-top: 3px; padding-left: 40px; background: url("' + img + '") no-repeat;">';
		html += '		Engage';
		html += '	</h2>';
		html += '</div>';
		
		return html;
	}
})();
