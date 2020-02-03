function blockElement($element, loaderImage) {
    $element.block({
        message: '<img src="' + loaderImage + '" alt="Loading..." />',
        overlayCSS: { backgroundColor: '#ffffff' },
        css: { border: 'none', backgroundColor: 'transparent' }
    });
};

function unblockElement($element) {
    $element.unblock();
};
