
$(document).ready(function(){    
 jQuery.fn.liScroll = function(settings) {
  settings = jQuery.extend({
    travelocity: 0.02
    }, settings);   
    return this.each(function(){
        var $strip = jQuery(this);
        $strip.addClass("newsticker")
        var stripHeight = 1;
        $strip.find("li").each(function(i){
          stripHeight += jQuery(this, i).outerHeight(true); // thanks to Michael Haszprunar and Fabien Volpi
        });
        var $mask = $strip.wrap("<div class='mask'></div>");
        var $tickercontainer = $strip.parent().wrap("<div class='tickercontainer'></div>");               
        var containerHeight = $strip.parent().parent().height();  //a.k.a. 'mask' width   
        $strip.height(stripHeight);     
        var totalTravel = stripHeight;
        var defTiming = totalTravel/settings.travelocity; // thanks to Scott Waye   
        function scrollnews(spazio, tempo){
        $strip.animate({top: '-='+ spazio}, tempo, "linear", function(){$strip.css("top", containerHeight); scrollnews(totalTravel, defTiming);});
        }
        scrollnews(totalTravel, defTiming);       
        $strip.hover(function(){
        jQuery(this).stop();
        },
        function(){
        var offset = jQuery(this).offset();
        var residualSpace = offset.top + stripHeight;
        var residualTime = residualSpace/settings.travelocity;
        scrollnews(residualSpace, residualTime);
        });     
    }); 
};

$(function(){
    $("ul#ticker01").liScroll();
});
 });

 $(document).ready(function ($) {
                // delegate calls to data-toggle="lightbox"
                $(document).on('click', '[data-toggle="lightbox"]:not([data-gallery="navigateTo"]):not([data-gallery="example-gallery"])', function(event) {
                    event.preventDefault();
                    return $(this).fancyLightbox({
                        onShown: function() {
                            if (window.console) {
                                return console.log('Checking our the events huh?');
                            }
                        },
            onNavigate: function(direction, itemIndex) {
                            if (window.console) {
                                return console.log('Navigating '+direction+'. Current item: '+itemIndex);
                            }
            }
                    });
                });

                // disable wrapping
                $(document).on('click', '[data-toggle="lightbox"][data-gallery="example-gallery"]', function(event) {
                    event.preventDefault();
                    return $(this).fancyLightbox({
                        wrapping: false
                    });
                });

                //Programmatically call
                $('#open-image').click(function (e) {
                    e.preventDefault();
                    $(this).fancyLightbox();
                });
                $('#open-youtube').click(function (e) {
                    e.preventDefault();
                    $(this).fancyLightbox();
                });

       
            });

/* ----------------------------------------------------------- */
  /* header sticky
  /* ----------------------------------------------------------- */
// $( document ).ready(function() {
// $('#alert').affix({
//     offset: {
//       top: 10, 
//       bottom: function () {
//       }
//     }
//   })  
// });

$(window).scroll(function(){
    if ($(window).scrollTop() >= 140) {
        $('#alert').addClass('sticky-top');
    }
    else {
        $('#alert').removeClass('sticky-top');
    }
});

//Check to see if the window is top if not then display button
jQuery(window).scroll(function(){
    if (jQuery(this).scrollTop() > 300) {
      jQuery('.scrollToTop').fadeIn();
    } else {
      jQuery('.scrollToTop').fadeOut();
    }
  });


   
  //Click event to scroll to top

  jQuery('.scrollToTop').click(function(){
    jQuery('html, body').animate({scrollTop : 0},300);
    return false;
  });  
//end Click event to scroll to top

 /* ==============================================
    Fixed menu
    =============================================== */
    
	$(window).on('scroll', function () {
		if ($(window).scrollTop() > 50) {
			$('.header_style_01').addClass('fixed-menu');
		} else {
			$('.header_style_01').removeClass('fixed-menu');
		}
	});