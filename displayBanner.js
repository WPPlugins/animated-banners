// animation defaults
var defaults={
	// styling
	padding:'60px 0px 60px 0px',
	textAlign:'center',
	fontSize:'15px',
	foreColour:'#eee',
	maskColour:'#000',
	maskOpacity:0.4,

	// animation timing
	slideDuration:800,
	fadeInDuration:200,
	holdDuration:2000,
	fadeOutDuration:200
};
function getSetting(setting,options){

	if('undefined' == typeof options){
		return defaults[setting];
	}
	if('undefined' == typeof options[setting]){
		return defaults[setting];
	}
	else return options[setting];
}

function displayBannerAfter(selector, text, options, delay){
	if(!delay) delay=0;
	setTimeout(function(){
		displayBanner(selector,text, options);
	}, delay);
}
function displayBanner(selector,text,options){

	// load this banner text (we will recurse later)
	thisText=text.shift();

	// loop through all images, generating their captions
	jQuery(selector).each(function(){

		bannerText = jQuery(document.createElement('div'))
			.html(thisText)
			.height('auto').width(jQuery(this).width())
			.css({fontSize:getSetting('fontSize',options), padding:getSetting('padding',options), textAlign:getSetting('textAlign',options)})
			.css({opacity:1, backgroundColor:'transparent', position:'absolute', zIndex:10, color:getSetting('foreColour',options)})
			.prependTo(jQuery(this).parent())
			.css('top',jQuery(this).position().top)
			.hide();

		bannerCanvas = jQuery(document.createElement('div'))
			.html(thisText)
			.height('auto').width(jQuery(this).width())
			.css({fontSize:getSetting('fontSize',options),padding:getSetting('padding',options),textAlign:getSetting('textAlign',options)})
			.css({backgroundColor:getSetting('maskColour',options), opacity:getSetting('maskOpacity',options), position:'absolute', zIndex:10, color:getSetting('maskColour',options)})
			.prependTo(jQuery(this).parent())
			.css('top',jQuery(this).position().top)
			.hide();

		jQuery(bannerCanvas).slideDown(getSetting('slideDuration',options),function(){
			jQuery(bannerText)
				.fadeIn(getSetting('fadeInDuration',options))
				.delay(getSetting('holdDuration',options))
				.fadeOut(getSetting('fadeOutDuration',options),function(){
					jQuery(bannerCanvas).hide('slow');
					if(text.length){
						displayBanner(selector,text,options);
					}
				});
		});
	});
}
