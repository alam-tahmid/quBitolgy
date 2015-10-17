gajQuery(document).ready(function() {
	gajQuery(".row0 a, .row1 a").fancybox({
		width:700,
		height:500,
		autoDimensions : false,
		transitionIn : 'elastic',
		transitionOut : 'elastic',
		speedIn : 600,
		speedOut : 200,
		'type'          :   'iframe'
	});
});