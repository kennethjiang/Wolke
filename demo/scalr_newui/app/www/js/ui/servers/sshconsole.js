Scalr.regPage('Scalr.ui.servers.sshconsole', function (loadParams, moduleParams) {
	return new Ext.Panel({
		title: 'Servers &raquo; ' + moduleParams['serverId'] + ' &raquo; SSH console',
		scalrOptions: {
			'maximize': 'all'
		},
		frame: true,
		tools: [{
			id: 'close',
			handler: function () {
				Scalr.Viewers.EventMessager.fireEvent('close');
			}
		}],
		layout: 'vbox',
		layoutConfig: {
			align: 'stretch',
			pack: 'start'
		},
		items: [{
			height: 30,
			html:
				'IP: ' + moduleParams['remoteIp'] + ' &nbsp; Internal IP: ' + moduleParams['localIp'] + '<br />' +
				'Farm: ' + moduleParams['farmName'] + ' (ID: ' + moduleParams['farmId'] + ') ' + 'Role: ' + moduleParams['roleName'] + '<br /><br />'
		}, {
			flex: 1,
			layout: 'fit',
			html: 'Loading, please wait ...',
			listeners: {
				afterrender: function () {
					(function() {
						this.body.update(
							'<APPLET CODE="com.mindbright.application.MindTerm.class" ARCHIVE="/java/mindterm.jar?r1" WIDTH="' + this.body.getWidth() + '" HEIGHT="' + this.body.getHeight() + '">' +
								'<PARAM NAME="sepframe" value="false">' +
								'<PARAM NAME="debug" value="false">' +
								'<PARAM NAME="quiet" value="true">' +
								'<PARAM NAME="menus" value="no">' +
								'<PARAM NAME="exit-on-logout" value="true">' +
								'<PARAM NAME="allow-new-server" value="false">' +
								'<PARAM NAME="savepasswords" value="false">' +
								'<PARAM NAME="verbose" value="false">' +
								'<PARAM NAME="useAWT" value="false">' +
								'<PARAM NAME="protocol" value="ssh2">' +
								'<PARAM NAME="server" value="' + moduleParams['remoteIp'] + '">' +
								'<PARAM NAME="port" value="' + moduleParams['port'] + '">' +
								'<PARAM NAME="username" value="root">' +
								'<PARAM NAME="auth-method" value="publickey">' +
								'<PARAM NAME="fg-color" value="white">' +
								'<PARAM NAME="bg-color" value="black">' +
								'<PARAM NAME="private-key-str" value="' + moduleParams['key'] + '">' +
								'<PARAM NAME="geometry" value="125x35">' +
							'</APPLET>'
						);
					}).defer(3000, this);
				}
			}
		}]
	});
});
