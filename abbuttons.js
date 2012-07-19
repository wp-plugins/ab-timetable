(function() {
    tinymce.create('tinymce.plugins.abplugin', {
        init : function(ed, url) {
            ed.addButton('abplugin', {
                title : 'AB Timetable - all week timetable',
                image : url + '/images/calendar.png',
                onclick : function() {
                     ed.selection.setContent('[abtimetable]');
 
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('abplugin', tinymce.plugins.abplugin);
})();