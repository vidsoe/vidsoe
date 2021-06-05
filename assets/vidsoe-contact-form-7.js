
if(!vidsoe.hasOwnProperty('contact_form_7')){
    vidsoe.contact_form_7 = {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        ace: {},

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        ace_edit: function(id){
            if(jQuery('#' + id).length){
            	jQuery('#' + id).hide();
            	jQuery('<div class="editor-container" id="' + id + '-editor-container"><div id="' + id + '-editor"></div></div>').insertBefore('#' + id);
                this.ace[id] = ace.edit(id + '-editor');
                this.ace[id].$blockScrolling = Infinity;
                this.ace[id].setOptions({
                	enableBasicAutocompletion: true,
                	enableLiveAutocompletion: true,
                	fontSize: 14,
                    maxLines: Infinity,
                    minLines: 5,
                    wrap: true,
                });
                this.ace[id].getSession().on('change', function(){
                    jQuery('#' + id).val(this.ace[id].getSession().getValue()).trigger('change');
                });
                this.ace[id].getSession().setMode('ace/mode/html');
                this.ace[id].getSession().setValue(jQuery('#' + id).val());
                this.ace[id].setTheme('ace/theme/monokai');
            }
        },

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        ace_destroy: function(id){
            if(typeof this.ace[id] !== 'undefined'){
                this.ace[id].destroy();
                jQuery('#' + id + '-editor-container').remove();
            	jQuery('#' + id).show();
            }
        },

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        ace_load: function(){
            if(typeof ace != 'undefined'){
                this.ace_edit('wpcf7-form');
                this.ace_mail();
                this.ace_mail_2();
                jQuery('#wpcf7-mail-use-html').on('change', this.ace_mail);
                jQuery('#wpcf7-mail-2-use-html').on('change', this.ace_mail_2);
        	}
        },

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        ace_mail: function(){
            if(jQuery('#wpcf7-mail-use-html').prop('checked')){
                this.ace_edit('wpcf7-mail-body');
            } else {
                this.ace_destroy('wpcf7-mail-body');
            }
        },

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        ace_mail_2: function(){
            if(jQuery('#wpcf7-mail-2-use-html').prop('checked')){
                this.ace_edit('wpcf7-mail-2-body');
            } else {
                this.ace_destroy('wpcf7-mail-2-body');
            }
        },

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
