jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.iptmp_plugin', {
        init : function(ed, url) {
                // Register command for when button is clicked
				
				// PS - Command for Insert Post Map
                ed.addCommand('iptmp_insert_shortcode', function() {
                    selected = tinyMCE.activeEditor.selection.getContent();

                    if( selected ){
                        //If text is selected when button is clicked
                        //Wrap shortcode around it.
                        content =  '[postmap]'+selected+'[/postmap]';
                    }else{
                        content =  '[postmap]';
                    }

                    tinymce.execCommand('mceInsertContent', false, content);
                });
				
				
				// PS - Command for Insert All Posts Map
				ed.addCommand('iptmp_insert_allposts_shortcode', function() {
                    ap_selected = tinyMCE.activeEditor.selection.getContent();

                    if( ap_selected ){
                        //If text is selected when button is clicked
                        //Wrap shortcode around it.
                        ap_content =  '[allpostmap]'+ap_selected+'[/allpostmap]';
                    }else{
                        ap_content =  '[allpostmap]';
                    }

                    tinymce.execCommand('mceInsertContent', false, ap_content);
                });

            // Register buttons - trigger above command when clicked
			ed.addButton('iptmp_button', {title : 'Insert Post Map', cmd : 'iptmp_insert_shortcode', image: '../wp-content/plugins/map-posts-free/images/iptmp_Insert_Map.png' });
			ed.addButton('iptmp_all_posts_button', {title : 'Insert All Posts Map', cmd : 'iptmp_insert_allposts_shortcode', image: '../wp-content/plugins/map-posts-free/images/iptmp_Insert_All_Posts_Map.png' });
        },   
    });

    // Register our TinyMCE plugin
    // first parameter is the button ID1
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('iptmp_button', tinymce.plugins.iptmp_plugin);
	tinymce.PluginManager.add('iptmp_all_posts_button', tinymce.plugins.iptmp_plugin);
});