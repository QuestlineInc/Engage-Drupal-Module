(function($) {
	var qlEditor;
	
	CKEDITOR.plugins.add('EngageArticleSearch', {
		icons: 'EngageArticleSearch',
		init: function(editor) {
			qlEditor = editor;
			
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
					buttons: [
						CKEDITOR.dialog.cancelButton
					],
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
						// Nothing to see... move along, move along...
					}
				};
			});
		}
	});
	
	function engageArticleSearchContent(pluginPath) {
		var iframeSrc = window.location.protocol + '//' + window.location.hostname + '/questline/search/dialog';
		
		var html = '';
		html += '<div style="width: 100%; height: 600px;">';
		html += '	<iframe id="ql_engage_article_search_iframe" src="' + iframeSrc + '" style="width: 100%; height: 100%;"></iframe>';
		html += '</div>';

		return html;
	}
	
	function engageInsertShortcodeIntoEditor(e) {
		qlEditor.insertHtml(e.data.message);
		CKEDITOR.dialog.getCurrent().hide();
	}
	
	if (window.addEventListener) {
		window.addEventListener("message", engageInsertShortcodeIntoEditor, false);        
	} 
	else if (window.attachEvent) {
		window.attachEvent("onmessage", engageInsertShortcodeIntoEditor, false);
	}
})(jQuery);
