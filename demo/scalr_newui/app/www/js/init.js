// fix problem with console.log
// TODO: console.production should work via cookies?
(function () {
	var global   = this;
	var original = global.console;
	var console  = global.console = {};
	console.production = false;

	if (original && !original.time) {
		original.time = function(name, reset){
			if (!name) return;
			var time = new Date().getTime();
			if (!console.timeCounters) console.timeCounters = {};

			var key = "KEY" + name.toString();
			if(!reset && console.timeCounters[key]) return;
			console.timeCounters[key] = time;
		};

		original.timeEnd = function(name){
			var time = new Date().getTime();

			if (!console.timeCounters) return;

			var key  = "KEY" + name.toString();
			var timeCounter = console.timeCounters[key];

			if (timeCounter) {
				var diff = time - timeCounter;
				var label = name + ": " + diff + "ms";
				console.info(label);
				delete console.timeCounters[key];
			}
			return diff;
		};
	}

	var methods = ['assert', 'count', 'debug', 'dir', 'dirxml', 'error', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd', 'trace', 'warn'];

	for (var i = methods.length; i--;) {
		(function (methodName) {
			console[methodName] = function () {
				if (original && methodName in original && !console.production) {
					original[methodName].apply(original, arguments);
				}
			};
		})(methods[i]);
	}
})();

// catch server error page (404, 403, timeOut and other)
Ext.Ajax.on('requestexception', function(conn, response, options) {
	var win = undefined;

	if (Ext.MessageBox.isVisible())
		Ext.MessageBox.hide();

		if (response.status == 403) {
			//Scalr.Viewers.ErrorMessage('Session expired. <a href="/login.html">Please login again.</a>');
			//return;

			if (win == undefined) {
				win = new Ext.Window({
					title: '<span style="color: #D50000">Session expired. Please login again</span>',
					modal: true,
					closable: false,
					resizable: false,
					draggable: false,
					bodyStyle: 'padding: 5px',
					buttonAlign: 'center',
					width: 400,
					height: 150,
					layout: 'hbox',
					layoutConfig: {
						align: 'stretch',
						pack: 'start'
					},
					items: [{
						width: 20,
						bodyStyle: 'background-color: inherit',
						border: false
					}, {
						flex: 1,
						layout: 'form',
						border: false,
						itemId: 'form',
						bodyStyle: 'background-color: inherit',
						labelWidth: 70,
						items: [{
							xtype: 'textfield',
							fieldLabel: 'E-mail',
							itemId: 'login',
							name: 'login',
							anchor: '-20',
							allowBlank: false,
							applyTo: Ext.get('body-login').child('[type="text"]')
						}, {
							xtype: 'textfield',
							fieldLabel: 'Password',
							inputType: 'password',
							itemId: 'pass',
							name: 'pass',
							anchor: '-20',
							allowBlank: false,
							applyTo: Ext.get('body-login').child('[type="password"]')
						}, {
							xtype: 'checkbox',
							name: 'keep_session',
							inputValue: 1,
							boxLabel: 'Keep me logged in until I log off',
							hideLabel: true
						}]
					}],
					buttons: [{
						text: 'Login',
						handler: function (comp) {
							var cont = comp.ownerCt.ownerCt.getComponent('form'), login = cont.getComponent('login'), pass = cont.getComponent('pass');

							login.isValid();
							pass.isValid();

							//Ext.get('body-login').child('form', true).submit(); // to save credentials need submit form, may be in future
							// TODO: check under who we logged in, may be it's another user'

							if (login.isValid() && pass.isValid()) {
								Ext.Ajax.request({
									url: '/login.php',
									params: {
										login: login.getValue(),
										pass: pass.getValue()
									},
									success: function (response) {
										var result = Ext.decode(response.responseText);
										if (result.result == 'ok') {
											win.hide();
											if (reloadPage)
												window.onhashchange();
											else
												Scalr.Viewers.InfoMessage('You have been logged in, but your previous request has not been performed due to lost session error. Please perform it again.');
										} else {
											login.markInvalid(result.message);
											pass.markInvalid(result.message);

										}
									}
								});
							}
						}
					}, {
						text: 'Cancel',
						handler: function () {
							document.location.href = '/login.html';
						}
					}]
				});
			}

		win.show();

	} else if (response.isTimeout) {
		Scalr.Viewers.ErrorMessage('Server didn\'t respond in time. Please try again in a few minutes.');
	} else {
		Scalr.Viewers.ErrorMessage('Cannot proceed your request at the moment. Please try again later.');
	}
});
