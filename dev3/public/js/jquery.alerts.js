/*
* File:			jquery.alerts.js
* Author:		Dennis White 
* Site:			http://jqalert.com
*/
(function($){
	$.Timer = function(callback, delay) {
	    var timerId, timerDelay;
	
	    timerDelay = delay;
	
	    this.pause = function() {
	        window.clearInterval(timerId);
	    };
	
	    callbackTimeout = function() {
	        callback();
	    };
	
	    this.resume = function() {
	        timerId = window.setInterval(callbackTimeout, timerDelay);
	    };
	
	    this.resume();
	    
	    return this;
	}
})(jQuery);

(function($) {
    if ($.ui) {
    	$.inform = function(message, options) {
    		var defaultOptions = {
    			title: 'Info',
    			icon: 'info',
    			customIcon:'',
				show: 'fade',
                hide: 'fade',
    			timer: -1,
            	allowEscape: false,
    			onTimeout: function(){ },
    			onClose: function(e, u) { }
            }
    		
    		if(options) {
    			defaultOptions = $.extend(defaultOptions, options);
    		}
    		
    		var $dialog = $('#informDialog');
    		$dialog.remove();
    		
            $dialog = $("<div id='informDialog' style='display:hidden' ></div>").appendTo('body');

            $('#infoMessage').remove();
            if(defaultOptions.icon === ''){
            	if(defaultOptions.customIcon === ''){
		            $("<p id='infoMessage' style='margin:5px; padding:0px;'>" + message + "</p>").appendTo('#informDialog');
            	} else {
		            $("<p id='infoMessage' ><i class='" + defaultOptions.customIcon + "' ></i>" + message + "</p>").appendTo('#informDialog');
            	}
            } else {
	            $("<p id='infoMessage' style='margin:5px; padding:0px;'><span class='ui-icon ui-icon-" + defaultOptions.icon + "' style='float:left; margin:5px 10px 20px 0px;'></span>" + message + "</p>").appendTo('#informDialog');
            }

			var timer;
            $dialog.dialog({
            	closeOnEscape: defaultOptions.allowEscape,
                resizable: false,
                height: 'auto',
                width: 'auto',
                title: defaultOptions.title,
				show: defaultOptions.show,
                hide: defaultOptions.hide,                
                modal: true,
                close: function(event, ui) {
                	if(typeof timer !== 'undefined'){
	                	timer.pause();
                	}
		            $dialog.remove();
                	defaultOptions.onClose(event, ui);
                },
                open: function(event, ui) { 
                	$(".ui-dialog-titlebar-close", ui.dialog).hide();
                	
                	if (defaultOptions.timer !== -1) {
	                	timer = $.Timer(function(){
	                		$dialog.dialog("close");
	                		timer.pause();
	                		defaultOptions.onTimeout();
	                	}, defaultOptions.timer); 
                	}
                }
            });
            
            return $dialog;    		
    	}
    	
        $.prompt = function(message, options) {
            var defaultOptions = {
                title: 'Prompt',
                icon: 'help', // just using the jquery ui icons
    			customIcon:'',
                defaultResult: '',
				show: 'fade',
                hide: 'fade',
    			timer: -1,
            	allowEscape: false,
    			onTimeout: function(){ },
    			onClose: function(e, u) { },
                buttons: [
                	{
                		title:"Ok",
                		callback: function() {$(this).dialog("close");},
                		css:""
                	},
                	{
                		title:"Cancel",
                		callback: function() {$(this).dialog("close");},
                		css:""
                	}
                ]
            };
            
            if (options)
                defaultOptions = $.extend(defaultOptions, options);
            
            var btns = {};
            $.each(defaultOptions.buttons, function(key, val) {
            	btns[val.title] = val.callback;
            });

            var $dialog = $('#promptDialog');
            $dialog.remove();

            $dialog = $("<div id='promptDialog' style='display:hidden' ></div>").appendTo('body');

            $('#promptMessage').remove();
            if(defaultOptions.icon === ''){
            	if(defaultOptions.customIcon === ''){
		            $("<p id='promptMessage' style='margin:5px; padding:0px;'>" + message + "</p>").appendTo('#promptDialog');
            	} else {
		            $("<p id='promptMessage'><i class='" + defaultOptions.customIcon + "'></i>" + message + "</p>").appendTo('#promptDialog');
	            }
            } else {
	            $("<p id='promptMessage'><span class='ui-icon ui-icon-" + defaultOptions.icon + "' style='float:left; margin:5px 10px 20px 0;'></span>" + message + "</p>").appendTo('#promptDialog');
            }
            
            $('<hr />').appendTo('#promptMessage');
            $("<input id='result' type='textbox' style='width:100%' value='" + defaultOptions.defaultResult + "' />").appendTo('#promptMessage');

			var timer;
            $dialog.dialog({
            	closeOnEscape: defaultOptions.allowEscape,
                resizable: false,
                height: 'auto',
                width: 'auto',
                title: defaultOptions.title,
				show: defaultOptions.show,
                hide: defaultOptions.hide,                
                modal: true,
                buttons: btns,
                close: function(event, ui) {
                	if(typeof timer !== 'undefined'){
	                	timer.pause();
                	}
		            $dialog.remove();
                	defaultOptions.onClose(event, ui);
                },
                open: function(event, ui) {
                	var i = 0; 
		            $.each(defaultOptions.buttons, function(key, val) {
		            	if('' !== val.callback) {
		            		var button1 = $('.ui-dialog-buttonset').children('button')[i];
							$(button1).addClass(val.css);
		            	}
		            	i++;
		            });
                	if (defaultOptions.timer !== -1) {
	                	timer = $.Timer(function(){
	                		$dialog.dialog("close");
	                		timer.pause();
			    			defaultOptions.onTimeout();
	                	}, defaultOptions.timer); 
                	}
                }
            });
            
            return $dialog;
        }

        $.confirm = function(message, options) {
            var defaultOptions = {
                title: 'Confirm',
                icon: 'help', // just using the jquery ui icons
    			customIcon:'',
				show: 'fade',
                hide: 'fade',
    			timer: -1,
            	allowEscape: false,
    			onTimeout: function(){ },
    			onClose: function(e, u) { },
                buttons: [
                	{
                		title:"Yes",
                		callback: function() {$(this).dialog("close");},
                		css:""
                	},
                	{
                		title:"No",
                		callback: function() {$(this).dialog("close");},
                		css:""
                	},
                	{
                		title:"Cancel",
                		callback: function() {$(this).dialog("close");},
                		css:""
                	}
                ]
            };

            if (options)
                defaultOptions = $.extend(defaultOptions, options);

            var btns = {};
            $.each(defaultOptions.buttons, function(key, val) {
            	btns[val.title] = val.callback;
            });

            var $dialog = $('#confirmDialog');
            $dialog.remove();

            $dialog = $("<div id='confirmDialog' style='display:hidden' ></div>").appendTo('body');

            $('#confirmMessage').remove();
            if(defaultOptions.icon === ''){
            	if(defaultOptions.customIcon === ''){
		            $("<p id='confirmMessage' style='margin:5px; padding:0px;'>" + message + "</p>").appendTo('#confirmDialog');
            	} else {
		            $("<p id='confirmMessage'><i class='" + defaultOptions.customIcon + "'></i>" + message + "</p>").appendTo('#confirmDialog');
	            }
            } else {
	            $("<p id='confirmMessage'><span class='ui-icon ui-icon-" + defaultOptions.icon + "' style='float:left; margin:5px 10px 20px 0;'></span>" + message + "</p>").appendTo('#confirmDialog');
            }

			var timer;
            $dialog.dialog({
            	closeOnEscape: defaultOptions.allowEscape,
                resizable: false,
                height: 'auto',
                width: 'auto',
                title: defaultOptions.title,
				show: defaultOptions.show,
                hide: defaultOptions.hide,                
                modal: true,
                buttons: btns,
                close: function(event, ui) {
                	if(typeof timer !== 'undefined'){
	                	timer.pause();
                	}
		            $dialog.remove();
                	defaultOptions.onClose(event, ui);
                },
                open: function(event, ui) {
                	var i = 0; 
		            $.each(defaultOptions.buttons, function(key, val) {
		            	if('' !== val.callback) {
		            		var button1 = $('.ui-dialog-buttonset').children('button')[i];
							$(button1).addClass(val.css);
		            	}
		            	i++;
		            });
                	if (defaultOptions.timer !== -1) {
	                	timer = $.Timer(function(){
	                		$dialog.dialog("close");
	                		timer.pause();
	                		defaultOptions.onTimeout();
	                	}, defaultOptions.timer); 
                	}
                }
            });
            
            return $dialog;
        }

        $.alert = function(message, options) {
            var defaultOptions = {
                title: 'Alert',
                icon: 'alert', // just using the jquery ui icons
    			customIcon:'',
                exception: '',
                stack: '',
				show: 'fade',
                hide: 'fade',
    			timer: -1,
            	allowEscape: false,
    			onTimeout: function(){ },
    			onClose: function(e, u) { },
                buttons: [
                	{
                		title:"Ok",
                		callback: function() {$(this).dialog("close");},
                		css:""
                	}
                ]
            };

            if (options) {
                defaultOptions = $.extend(defaultOptions, options);
            }

            var dlgWidth = 'auto';

            var btns = {};
            $.each(defaultOptions.buttons, function(key, val) {
            	btns[val.title] = val.callback;
            });

            var $dialog = $('#alertDialog');
            $dialog.remove();

            $dialog = $("<div id='alertDialog' style='display:hidden' ></div>").appendTo('body');

            $('#alertMessage').remove();
            if(defaultOptions.icon === ''){
            	if(defaultOptions.customIcon === ''){
		            $("<p id='alertMessage' style='margin:5px; padding:0px;'>" + message + "</p>").appendTo('#alertDialog');
            	} else {
		            $("<p id='alertMessage'><i class='" + defaultOptions.customIcon + "'></i>" + message + "</p>").appendTo('#alertDialog');
	            }
            } else {
	            $("<p id='alertMessage'><span class='ui-icon ui-icon-" + defaultOptions.icon + "' style='float:left; margin:5px 10px 20px 0;'></span>" + message + "</p>").appendTo('#alertDialog');
            }

            $('#alertException').remove();
            if (defaultOptions.exception != '') {
                if ('' != defaultOptions.stack) {
                    $("<div id='alertException'><hr /></div>").appendTo('#alertDialog');
                    $("<p ><strong>" + defaultOptions.exception + "</strong> " + defaultOptions.stack + "</p>").appendTo('#alertException');
                    // stack traces can be BIG so set the max width
                    dlgWidth = '960';
                }
                else {
                    $("<div id='alertException'></div>").appendTo('#alertDialog');
                    $("<p >" + defaultOptions.exception + "</p>").appendTo('#alertException');
                }
            }

            var timer;
            $dialog.dialog({
            	closeOnEscape: defaultOptions.allowEscape,
                resizable: false,
                height: 'auto',
                width: dlgWidth,
                title: defaultOptions.title,
				show: defaultOptions.show,
                hide: defaultOptions.hide,                
                modal: true,
                buttons: btns,
                close: function(event, ui) {
                	if(typeof timer !== 'undefined'){
	                	timer.pause();
                	}
    	            $dialog.remove();
                	defaultOptions.onClose(event, ui);
                },
                open: function(event, ui) {
                	var i = 0; 
		            $.each(defaultOptions.buttons, function(key, val) {
		            	if('' !== val.callback) {
		            		var button1 = $('.ui-dialog-buttonset').children('button')[i];
							$(button1).addClass(val.css);
		            	}
		            	i++;
		            });
                	if (defaultOptions.timer !== -1) {
	                	timer = $.Timer(function(){
	                		$dialog.dialog("close");
	                		timer.pause();
	                		defaultOptions.onTimeout();
	                	}, defaultOptions.timer); 
                	}
                }
            });
            
            return $dialog;
        }
    }
})(jQuery);