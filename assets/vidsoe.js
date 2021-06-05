
if(typeof vidsoe === 'undefined'){
    vidsoe = {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        add_query_arg: function(key, value, url){
            'use strict';
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
                search_object = vidsoe.parse_str(a.search);
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
            'use strict';
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
                search_object = vidsoe.parse_str(a.search);
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

    };
}
