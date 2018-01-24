/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductLabel
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
define([
    "jquery",
    "jquery/ui"
], function($) {
    "use strict";
    var label = {};

    //display label on product page on mobile
    label.productOnMobile = function(className,pos,height_img,height_label)
    {
        $('.outofstock-label').width('70px');
        $('.newproduct-label').width('70px');
        $('.saleproduct-label').width('70px');
        $(className).css('position','absolute');
        $(className).css('z-index','1');
        switch (pos){
            case "right-top":
                $(className).css('right',"10px");
                $(className).css('top',"10px");
                $(className).css('left',"none");
                break;
            case "right-bottom":
                $(className).css('right',"10px");
                $(className).css('top',height_img-height_label-10);
                $(className).css('left',"none");
                break;
            case "left-bottom":
                $(className).css('left','10px');
                $(className).css('top',height_img-height_label-10);
                break;
            case "left-top":
                $(className).css('left','10px');
                $(className).css('top',"10px");
                break;
        }
    }

    //display label on product page on window
    label.productOnWindow=function (className,pos,width,height,height_label) {
        $('.outofstock-label').width('100px');
        $('.newproduct-label').width('100px');
        $('.saleproduct-label').width('100px');
        $(className).css('position','absolute');
        $(className).css('z-index','1');
        switch (pos){
            case "right-top":
                $(className).css('left',width-10);
                $(className).css('top','20px');
                $(className).css('right',"none");
                break;
            case "right-bottom":
                $(className).css('left',width-10);
                $(className).css('top',height-height_label*2);
                $(className).css('right',"none");
                break;
            case "left-bottom":
                $(className).css('left',"50px");
                $(className).css('top',height-height_label*2);
                break;
            case "left-top":
                $(className).css('left','50px');
                $(className).css('top','20px');
                break;
        }
    }

    //display label on product list page grid mode
    label.onProductGrid=function(className,pos){
        $(className).css('position','absolute');
        $(className).css('z-index','1');
        switch (pos){
            case "right-top":
                $(className).css('right','0px');
                $(className).css('top','0px');
                break;
            case "right-bottom":
                $(className).css('right','0px');
                $(className).css('margin-top','-60px');
                break;
            case "left-bottom":
                $(className).css('left','0px');
                $(className).css('margin-top','-60px');
                break;
            case "left-top":
                $(className).css('left','0px');
                $(className).css('top','0px');
                break;
        }
    }

    //display label on product list page list mode
    label.onProductList=function(className,pos,height){
        $('.list-mode').css('position','relative')
        $(className).css('position','absolute');
        $(className).css('z-index','1');
        switch (pos){
            case "right-top":
                $(className).css('right','0px');
                $(className).css('top',-height);
                break;
            case "right-bottom":
                $(className).css('right','0px');
                $(className).css('margin-top','-60px');
                break;
            case "left-bottom":
                $(className).css('left','0px');
                $(className).css('margin-top','-60px');
                break;
            case "left-top":
                $(className).css('left','0px');
                $(className).css('top',-height);
                break;
        }
    }

    return label;
});