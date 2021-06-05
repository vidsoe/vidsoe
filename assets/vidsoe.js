
if(typeof vidsoe === 'undefined'){
    vidsoe = {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        add_query_arg: function(key, value, url){
            var a = {}, href = '', search = [], search_object = {};
            a = document.createElement('a');
            if(url === ''){
                a.href = jQuery(location).attr('href');
            } else {
                a.href = url;
            }
            if(a.protocol){
                href += a.protocol + '//';
            }
            if(a.hostname){
                href += a.hostname;
            }
            if(a.port){
                href += ':' + a.port;
            }
            if(a.pathname){
                if(a.pathname[0] !== '/'){
                    href += '/';
                }
                href += a.pathname;
            }
            if(a.search){
                search_object = this.parse_str(a.search);
                jQuery.each(search_object, function(k, v){
                    if(k != key){
                        search.push(k + '=' + v);
                    }
                });
                if(search.length > 0){
                    href += '?' + search.join('&') + '&';
                } else {
                    href += '?';
                }
            } else {
                href += '?';
            }
            href += key + '=' + value;
            if(a.hash){
                href += a.hash;
            }
            return href;
        },

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        add_query_args: function(args, url){
            var a = {}, href = '', search = [], search_object = {};
            a = document.createElement('a');
            if(url === ''){
                a.href = jQuery(location).attr('href');
            } else {
                a.href = url;
            }
            if(a.protocol){
                href += a.protocol + '//';
            }
            if(a.hostname){
                href += a.hostname;
            }
            if(a.port){
                href += ':' + a.port;
            }
            if(a.pathname){
                if(a.pathname[0] !== '/'){
                    href += '/';
                }
                href += a.pathname;
            }
            if(a.search){
                search_object = this.parse_str(a.search);
                jQuery.each(search_object, function(k, v){
                    if(!(k in args)){
                        search.push(k + '=' + v);
                    }
                });
                if(search.length > 0){
                    href += '?' + search.join('&') + '&';
                } else {
                    href += '?';
                }
            } else {
                href += '?';
            }
            jQuery.each(args, function(k, v){
                href += k + '=' + v;
            });
            if(a.hash){
                href += a.hash;
            }
            return href;
        },

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        page_visibility_event: function(){
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

        parse_str: function(){
            var i = 0, search_object = {}, search_array = [];
            search_array = str.replace('?', '').split('&');
            for(i = 0; i < search_array.length; i ++){
                search_object[search_array[i].split('=')[0]] = search_array[i].split('=')[1];
            }
            return search_object;
        },

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        parse_url: function(){
            var a = {}, components = {}, keys = [];
            a = document.createElement('a');
            keys = ['protocol', 'hostname', 'port', 'pathname', 'search', 'hash'];
            if(url === ''){
                a.href = jQuery(location).attr('href');
            } else {
                a.href = url;
            }
            if(typeof component === 'undefined' || component === ''){
                jQuery.map(keys, function(c){
                    components[c] = a[c];
                });
                return components;
            } else if(jQuery.inArray(component, keys) !== -1){
                return a[component];
            } else {
                return '';
            }
        },

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        rem_to_px: function(count){
            var unit = '';
            unit = jQuery('html').css('font-size');
        	if(typeof count !== 'undefined' && count > 0){
        		return (parseInt(unit) * count);
        	} else {
        		return parseInt(unit);
        	}
        },

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    };
}
