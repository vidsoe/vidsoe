if('undefined' === typeof(vidsoe.floating_labels)){
    vidsoe.floating_labels = {

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        both: function(){
            if(jQuery('.vidsoe-floating-labels > textarea').length){
                jQuery('.vidsoe-labels > textarea').each(function(){
                    vidsoe.floating_labels.textarea(this);
                });
            }
            if(jQuery('.vidsoe-floating-labels > select').length){
                jQuery('.vidsoe-floating-labels > select').each(function(){
                    vidsoe.floating_labels.select(this);
                });
            }
        },

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        init: function(){
            jQuery(document).on('ready', function(){
                if(jQuery('.vidsoe-floating-labels > textarea').length){
                    jQuery('.vidsoe-floating-labels > textarea').each(function(){
                        jQuery(this).data({
                            'border': jQuery(this).outerHeight() - jQuery(this).innerHeight(),
                            'element': jQuery(this).height(),
                            'padding': jQuery(this).innerHeight() - jQuery(this).height(),
                        });
                    });
                }
                vidsoe.floating_labels.both();
                if(jQuery('.vidsoe-floating-labels > textarea').length){
                    jQuery('.vidsoe-floating-labels > textarea').on('input propertychange', function(){
                        vidsoe.floating_labels.textarea(this);
                    });
                }
                if(jQuery('.vidsoe-floating-labels > select').length){
                    jQuery('.vidsoe-floating-labels > select').on('change', function(){
                        vidsoe.floating_labels.select(this);
                    });
                }
            });
            jQuery(document).on(vidsoe.floating_labels.page_visibility_event(), vidsoe.floating_labels.both);
        },

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        page_visibility_event: function(){
            'use strict';
            var visibilityChange = '';
            if(typeof document.hidden !== 'undefined'){ // Opera 12.10 and Firefox 18 and later support
                visibilityChange = 'visibilitychange';
            } else if(typeof document.webkitHidden !== 'undefined'){
                visibilityChange = 'webkitvisibilitychange';
            } else if(typeof document.msHidden !== 'undefined'){
                visibilityChange = 'msvisibilitychange';
            } else if(typeof document.mozHidden !== 'undefined'){ // Deprecated
                visibilityChange = 'mozvisibilitychange';
            }
            return visibilityChange;
        },

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        page_visibility_state: function(){
            'use strict';
            var hidden = '';
            if(typeof document.hidden !== 'undefined'){ // Opera 12.10 and Firefox 18 and later support
                hidden = 'hidden';
            } else if(typeof document.webkitHidden !== 'undefined'){
                hidden = 'webkitHidden';
            } else if(typeof document.msHidden !== 'undefined'){
                hidden = 'msHidden';
            } else if(typeof document.mozHidden !== 'undefined'){ // Deprecated
                hidden = 'mozHidden';
            }
            return document[hidden];
        },

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        select: function(select){
            if(jQuery(select).val() == ''){
                jQuery(select).removeClass('placeholder-hidden');
            } else {
                jQuery(select).addClass('placeholder-hidden');
            }
        },

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        textarea: function(textarea){
            jQuery(textarea).height(parseInt(jQuery(textarea).data('element'))).height(textarea.scrollHeight - parseInt(jQuery(textarea).data('padding')));
        },

    	// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    };
}
