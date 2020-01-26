var zikulacontentmodule = function(quill, options) {
    setTimeout(function() {
        var button;

        button = jQuery('button[value=zikulacontentmodule]');

        button
            .css('background', 'url(' + Zikula.Config.baseURL + Zikula.Config.baseURI + '/public/modules/zikulacontent/images/admin.png) no-repeat center center transparent')
            .css('background-size', '16px 16px')
            .attr('title', 'Content')
        ;

        button.click(function() {
            ZikulaContentModuleFinderOpenPopup(quill, 'quill');
        });
    }, 1000);
};
