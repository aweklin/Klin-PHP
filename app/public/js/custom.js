// plugins
(function($) {
    
    /**
     * Displays message to the user
     * 
     * @param type Determines the style applied to the message being displayed. Use e, i, w, s to represent error, info, warning and success message respectively
     * @param msg The actual message to be displayed.
     */
	$.fn.showMsg = function(type, msg) {
        this.html('');
		this.hide();
		
		if (msg !== null && msg !== '') {
			var msgType = '';
			
			switch(type) {
				case '1':
				case 'e':
				case 'ex':
				case 'err':
				case 'error':
					msgType = 'danger';
					break;
				case '2':
				case 'w':
				case 'warn':
				case 'warning':
					msgType = 'warning';
					break;
				case '3':
				case 'i':
				case 'info':
					msgType = 'info';
					break;
				case '0':
				case 's':
				case 'success':
					msgType = 'success';
					break;
				default:
					msgType = 'default';
					break;
			}
			
			this.html('<div class="alert alert-' + msgType + '" alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button"> × </button>' + msg + '</div>')
			this.show();
		}
		
		return this;
    };

}(jQuery));