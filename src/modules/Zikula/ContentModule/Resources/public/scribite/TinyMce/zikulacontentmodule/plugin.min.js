/**
 * Initializes the plugin, this will be executed after the plugin has been created.
 * This call is done before the editor instance has finished it's initialization so use the onInit event
 * of the editor instance to intercept that event.
 *
 * @param {tinymce.Editor} ed Editor instance that the plugin is initialised in
 * @param {string} url Absolute URL to where the plugin is located
 */
tinymce.PluginManager.add('zikulacontentmodule', function(editor, url) {
    var icon;

    icon = Zikula.Config.baseURL + Zikula.Config.baseURI + '/web/modules/zikulacontent/images/admin.png';

    editor.addButton('zikulacontentmodule', {
        //text: 'Content',
        image: icon,
        onclick: function() {
            ZikulaContentModuleFinderOpenPopup(editor, 'tinymce');
        }
    });
    editor.addMenuItem('zikulacontentmodule', {
        text: 'Content',
        context: 'tools',
        image: icon,
        onclick: function() {
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
