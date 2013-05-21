$(document).ready(function() {
  var total = 3;
	for(i=0;i<=total; i++){
		if ( i == 0 ) {
			$('ul.rslides').append('<li><img src="../../content/slides/slide.jpg" alt=""></li>');
		} else {
			$('ul.rslides').append('<li><img src="../../content/slides/slide ('+i+').jpg" alt=""></li>');
		}
	}
});
