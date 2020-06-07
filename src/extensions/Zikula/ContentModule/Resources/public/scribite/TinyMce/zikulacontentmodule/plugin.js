/**
 * Initializes the plugin, this will be executed after the plugin has been created.
 * This call is done before the editor instance has finished it's initialization so use the onInit event
 * of the editor instance to intercept that event.
 *
 * @param {tinymce.Editor} ed Editor instance that the plugin is initialised in
 * @param {string} url Absolute URL to where the plugin is located
 */
tinymce.PluginManager.add('zikulacontentmodule', function(editor, url) {
    editor.ui.registry.addButton('zikulacontentmodule', {
        icon: 'link',
        tooltip: 'Content',
        onAction: function() {
            ZikulaContentModuleFinderOpenPopup(editor, 'tinymce');
        }
    });
    editor.ui.registry.addMenuItem('zikulacontentmodule', {
        text: 'Content',
        icon: 'link',
        onAction: function() {
            ZikulaContentModuleFinderOpenPopup(editor, 'tinymce');
        }
    });

    return {
        getMetadata: function() {
            return {
                title: 'Content',
                url: 'https://ziku.la'
            };
        }
    };
});
