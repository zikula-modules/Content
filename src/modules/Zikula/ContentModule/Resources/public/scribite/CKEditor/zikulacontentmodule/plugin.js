CKEDITOR.plugins.add('zikulacontentmodule', {
    requires: 'popup',
    init: function (editor) {
        editor.addCommand('insertZikulaContentModule', {
            exec: function (editor) {
                ZikulaContentModuleFinderOpenPopup(editor, 'ckeditor');
            }
        });
        editor.ui.addButton('zikulacontentmodule', {
            label: 'Content',
            command: 'insertZikulaContentModule',
            icon: this.path.replace('scribite/CKEditor/zikulacontentmodule', 'images') + 'admin.png'
        });
    }
});
