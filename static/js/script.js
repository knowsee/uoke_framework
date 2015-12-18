
/*  ------------------
    Remove Preloader
    ------------------  */

$(window).load(function () {
    $('#preloader').delay(350).fadeOut('slow', function () {
        $('.profile-page, .portfolio-page, .service-page, .contact-page').hide();
    });
});

$(document).ready(function () {

    'use strict';

    /*  ---------------------
         Homepage Responsive
        ---------------------  */

    function homepageResponsive() {

        // Homepage Main Portions Responsive

        var windowsWidth = $(window).width(),
            windowsHeight = $(window).height();
        

        // Homepage Profile Image Responsive

        var introWidth = $('.introduction').width(),
            introHeight = $('.introduction').height(),
            bgImage = $('.introduction').find('img'),
            menuBgImages = $('.menu > div img');

        if (introWidth > introHeight) {

            bgImage.css({
                width: '100%',
                height: '100%'
            });
            menuBgImages.css({
                width: '100%',
                height: '100%'
            });

        } else {

            bgImage.css({
                width: 'auto',
                height: '100%'
            });
            menuBgImages.css({
                width: '100%',
                height: '100%'
            });

        }
		
		if (windowsWidth > 767) {

            $('.introduction , .menu').css({
                width: '50%',
                height: '100%'
            });
			
        } else {
			if(windowsHeight < 500) {
				$('.introduction').css({
					width: '100%',
					height: '60%'
				});
				$('.menu').css({
					width: '100%',
					height: '40%'
				});
				$('.menu i').hide();
				$('.menu img').css('height', 'auto');
				$('.profile-btn, .portfolio-btn, .service-btn, .contact-btn').css({
					height: '35%'
				});
			} else {
				$('.introduction').css({
					width: '100%',
					height: '55%'
				});
				$('.menu').css({
					width: '100%',
					height: '45%'
				});
			}
        }

    }

    $(window).on('load resize', homepageResponsive);

    /*  --------------
         Menu Settings
        --------------  */

    // Hide Menu

    $('.menu > div').on('click', function () {

        var introWidth = $('.introduction').width(),
            menuWidth = $('.menu').width();

        $('.introduction').animate({
            left: '-' + introWidth
        }, 1000, 'easeOutQuart');
        $('.menu').animate({
            left: menuWidth
        }, 1000, 'easeOutQuart', function () {
            $('.home-page').css({
                visibility: 'hidden'
            });
        });

    });


    $('.menu div.profile-btn').on('click', function () {
        $('.profile-page').fadeIn(1200);
        setTimeout(function(){
            $('.count').each(function () {
                $(this).prop('Counter',0).animate({
                    Counter: $(this).text()
                }, {
                    duration: 7500,
                    easing: 'swing',
                    step: function (now) {
                        $(this).text(Math.ceil(now));
                    }
                });
            });
        }, 100);
    });

    $('.menu div.portfolio-btn').on('click', function () {
        $('.portfolio-page').fadeIn(1200);
        setTimeout(function(){
            $('#projects').mixItUp();
        }, 100);
    });

    $('.menu div.service-btn').on('click', function () {
        $('.service-page').fadeIn(1200);
    });

    $('.menu div.contact-btn').on('click', function () {
        $('.contact-page').fadeIn(1200);
        setTimeout(function(){
            google.maps.event.trigger(map,'resize');
        },100);
    });


    // Close Button, Hide Menu

    $('.close-btn').on('click', function () {
        $('.home-page').css({
            visibility: 'visible'
        });
        $('.introduction, .menu').animate({
            left: 0
        }, 1000, 'easeOutQuart');
        $('.profile-page, .portfolio-page, .service-page, .contact-page').fadeOut(800);
    });

    /*  ----------------------------------------
         Tooltip Starter for Social Media Icons
        ----------------------------------------  */

    $('.intro-content .social-media [data-toggle="tooltip"]').tooltip({
        placement: 'bottom'
    });

    $('.contact-details .social-media [data-toggle="tooltip"]').tooltip();



    /*----------------------script for owl carousel sponsors---------------------*/

        $("#sponsor-list").owlCarousel({
                 
            autoPlay: 3000, //Set AutoPlay to 3 seconds
            stopOnHover: true,
            items : 3,
            itemsDesktop: [1200,3],
            itemsDesktopSmall: [991,3],
            itemsTablet: [767,2],
            itemsTabletSmall: [625,2],
            itemsMobile: [479,1]
        });



    /*  -------------------------------
         PopUp ( for portfolio page )
        -------------------------------  */

    $(function () {
        $('.show-popup').popup({
            keepInlineChanges: true,
            speed: 500
        });
    });


    

});
