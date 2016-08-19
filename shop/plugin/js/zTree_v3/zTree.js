// JavaScript Document
define(function(require, exports, module){
	require('jquery');
	require('./js/jquery.ztree.core-3.5.js');
	require('./css/zTreeStyle/zTreeStyle.css');	

	var setting = {
			async: {
				enable: true,
				url:"/include/js/zTree_v3/demo/cn/asyncData/getNodes.php",
				autoParam:["id", "name=n", "level=lv"],
				otherParam:{"otherParam":"zTreeAsyncTest"},
				dataFilter: filter
			}
		};

		function filter(treeId, parentNode, childNodes) {
			if (!childNodes) return null;
			for (var i=0, l=childNodes.length; i<l; i++) {
				childNodes[i].name = childNodes[i].name.replace(/\.n/g, '.');
			}
			return childNodes;
		}
	
	
	
	
	exports.init=function(){
		$(document).ready(function(){
			$.fn.zTree.init($("#treeDemo"), setting);
		});
	};
});