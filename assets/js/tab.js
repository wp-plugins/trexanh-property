jQuery(document).ready(function() {
    var tabLi = jQuery(".tab-links li");
    jQuery('.next-step-link').on('click', function(e)  {
        var next = 0;
        tabLi.each(function(index, elem) {
            if (jQuery(elem).hasClass("active")) {
                jQuery(elem).removeClass("active");
                next = index + 1;
            }
        });
        tabLi.eq(next).find("a").trigger("click");
        e.preventDefault();
    });
    jQuery('.back-step-link').on('click', function(e)  {
        var previous = 0;
        tabLi.each(function(index, elem) {
            if (jQuery(elem).hasClass("active")) {
                jQuery(elem).removeClass("active");
                previous = index - 1;
            }
        });
        tabLi.eq(previous).find("a").trigger("click");
        e.preventDefault();
    });
    jQuery('.tabs .tab-links a').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');
        jQuery('.tabs .tab').removeClass("active");
        jQuery('.tabs ' + currentAttrValue).addClass("active");
        // Change/remove current tab to active
        jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
        e.preventDefault();
    });
});