navigator
.require(CB_JS_TRANSPORT.paths.mods+'/Util.js')
.require(CB_JS_TRANSPORT.paths.thirdParty+'/messg.js');

navigator.define('Cb\Notify', [
	'Util', 
	'@window.messg' // See: https://github.com/andrepolischuk/messg
], function ($, undefined) {
	var util = navigator.mod('Util'),
		typeMap = {
			0: 'error',
			1: 'success',
			2: 'warning'
		};
	util.css(CB_JS_TRANSPORT.paths.baseUri + '/css/css-plugins/messg.css');
	// See: http://www.jqueryrain.com/demo/jquery-notification-plugin/page/2/
	// See: https://github.com/andrepolischuk/messg
	messg.set('position  ', 'top');
	return {
		notify: function (_msg, _type, _displayTime) {
			_displayTime = _displayTime || 8e3; // defaults to 8 sec display time
			if (_type === undefined) { _type = 2; }			
			messg[typeMap[util.pint(_type)]](_msg, _displayTime);	
		}
	};
});