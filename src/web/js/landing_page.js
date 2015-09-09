// The JavaScript for the landing page


$(document).ready(function() {
	
	var stackheight = $(window).height();
	
	$(window).resize(function() {
		stackheight = $(window).height();
		$('.stackview').css('height', stackheight);
	});
  
    var keywords = ['artificial intelligence', 'descartes', 'sartre', 'cognition', 'psychology'];
    
    word = keywords[getRandomInt(0,keywords.length-1)];
  $('#landing-stack').stackView({url: 'translators/cloud.php', query: word, ribbon: word});
  
  /**$('#landing-stack').stackView({
      url: 'js/awesome.json', 
      ribbon: 'Recent Awesome Returns'
  });**/
	
	$('.stackview').css('height', stackheight);
	
});

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}
