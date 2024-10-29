/**
 * We use the initCallback callback
 * to assign functionality to the controls
**/

jQuery.noConflict();

jQuery(document).ready(function() {

    jQuery("#mycarousel").jcarousel({
									
        start: parseInt(jc_options.start),
        size: jc_options.size,
        scroll: 1, //parseInt(jc_options.scroll),
        auto: parseInt(jc_options.length),
        wrap: 'last',
        initCallback: mycarousel_initCallback,
        // This tells jCarousel NOT to autobuild prev/next buttons
        // buttonNextHTML: null,
        // buttonPrevHTML: null,
		
		itemVisibleInCallback: mycarousel_itemVisibleInCallback
    });

});

function mycarousel_itemVisibleInCallback(carousel, item, idx, state) {
	var spaces = 7; // Hardcoded: must be ODD not even
	var total = carousel.size(); // total number of items in the carousel
	var shoulder = Math.ceil(spaces / 2); // margin on either end where there is a single break in stead of two
	var midboxes = spaces - 4; // number of boxes in the middle when there are two breaks
        
        // set an active class
        jQuery('#mycarousel .jcarousel-control a').each(function(i) {
                if(i == idx - 2)
                        jQuery(this).addClass('active-guru-item');
                else
                        jQuery(this).removeClass('active-guru-item');
        });
        
        if(total <= spaces) return; // no compression unless required
	
	// remove existing compression breaks
	jQuery('#mycarousel .jcarousel-control span.compression-break').remove();
		
	if(idx <= shoulder) {
		// LEFT MARGIN
		
		jQuery('#mycarousel .jcarousel-control a').each(function(i) {
			// show left shoulder + last / hide remainder
			if(i < spaces - 2 || i == total - 1) jQuery(this).show();
			else jQuery(this).hide();
			
			// add single compression break after first
			if(i + 1 == total - 1)
				jQuery(this).after('<span class="compression-break">...</span>');
		});
	}
	else if(idx - 1 >= total - shoulder) {
		// RIGHT MARGIN
		
		jQuery('#mycarousel .jcarousel-control a').each(function(i) {
			// show first + right shoulder / hide remainder
			if(i == 0 || i > total - spaces + 1) jQuery(this).show();
			else jQuery(this).hide();
			
			// add single compression break before last
			if(i == 0)
				jQuery(this).after('<span class="compression-break">...</span>');
		});
	}
	else {
		// MIDDLE SET with double break
		
		var midbegin = idx - Math.floor(midboxes / 2);
		
		jQuery('#mycarousel .jcarousel-control a').each(function(i) {
			// show first + middle set + last / hide remainder
			if(i == 0 || (i + 1 >= midbegin &&  i + 1 < midbegin + midboxes) || i >= total - 1) jQuery(this).show();
			else jQuery(this).hide();
			
			// add two compression breaks: after first and before last
			if(i == 0 || i + 1 == total - 1)
				jQuery(this).after('<span class="compression-break">...</span>');
		});	
	}
}

function mycarousel_initCallback(carousel) {

    jQuery('.jcarousel-control a').bind('click', function() {
        carousel.scroll(jQuery.jcarousel.intval(jQuery(this).text()));
        return false;
    });

    jQuery('#mycarousel-next').bind('click', function() {
        carousel.next();
        return false;
    });

    jQuery('#mycarousel-prev').bind('click', function() {
        carousel.prev();
        return false;
    });

};