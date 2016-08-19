define(function(require, exports, module){
	require('jquery');
	require('./js/jquery.splitter-0.8.0.js');
	require('./css/jquery.splitter.css');	
	
	exports.init=function(){
		jQuery(function($){   
		   $('#spliter').css({width: '100%', height: '100%'}).split({orientation: 'horizontal', limit: 20,position:'90px'});
		   $('#main-panel').split({orientation:'vertical', limit:10,position:'200px'});
		});
	};
});