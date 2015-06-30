/* dialog */
(function($) {
    if (!window.Txl) {
        window.Txl = {};
    }
    window.Txl.Dialog = function(title, message, buttons) {
        var me = this;
        this.mode = 'confirm';
        this.dlg = $("<div />").appendTo("body");
        this.buttons = {
            ok: {
                'text' : 'OK',
                'class' : 'button-primary',
                'click' : function() {
                    if (me.okCallback && me.mode == 'confirm') {
                        me.okCallback();
                    }
                    me.close();
                }
            },
            cancel: {
                'text' : 'Cancel',
                'class' : 'button-default',
                'click' : function() {
                    if (me.cancelCallback && me.mode == 'confirm') {
                        me.cancelCallback();
                    }
                    me.close();
                }
            }
        };
        this.dlg.dialog({
            'dialogClass' : 'alert',
            'modal' : true,
            'autoOpen' : false,
            'closeOnEscape' : false,
            'draggable' : false,
            'minWidth' : 400,
            'buttons' : [this.buttons.cancel, this.buttons.ok]
        });
        if (title) {
            this.setTitle(title);
        }
        if (message) {
            this.setMessage(message);
        }
        if (buttons) {
            this.setButtons(buttons);
        }
    };
    window.Txl.Dialog.prototype.open = function() {
        this.dlg.dialog('open');
    };
    window.Txl.Dialog.prototype.close = function() {
        this.dlg.dialog('close');
    };
    window.Txl.Dialog.prototype.setTitle = function(title) {
        this.dlg.dialog( "option", "title", title);
    };
    window.Txl.Dialog.prototype.setMessage = function(message) {
        this.dlg.html( message );
    };
    window.Txl.Dialog.prototype.setOkCallback = function(callback) {
        this.okCallback = callback;
    };
    window.Txl.Dialog.prototype.setCancelCallback = function(callback) {
        this.cancelCallback = callback;
    };
    /**
     * mode: alert --> only OK button, no callbacks
     * mode: confirm --> 2 buttons OK, Cancel with callbacks
     * @param string mode
     */
    window.Txl.Dialog.prototype.setMode = function(mode) {
        if (mode !== 'alert' && mode !== 'confirm') {
            mode = 'confirm';
        }
        var buttons = [];
        this.mode = mode;
        if (mode == 'alert') {
            buttons= [this.buttons.ok];
        }
        if (mode == 'confirm') {
            buttons = [this.buttons.cancel, this.buttons.ok];
        }
        this.dlg.dialog( "option", "buttons", buttons );
    };
})(jQuery);