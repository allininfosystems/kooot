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
require([
    'jquery',
    'Bss_ProductLabel/js/label'
], function ($,label) {
    $('.table-comparison .cell').css('position','relative');
    $('.outofstock-list').width('70px');
    $('.newproduct-list').width('70px');
    $('.saleproduct-list').width('70px');
    $('.product-item-info').css('position','relative');
    var pos1,pos2,pos3;
    var mode=$('#product-list-mode').html();
    if($('#compare-page').html()>0) {
        $('.outofstock-list').width('50px');
        $('.newproduct-list').width('50px');
        $('.saleproduct-list').width('50px');
    }
    if(mode=='list')
    {
        var width = $(window).width();
        //Out Of Stock Label
        if(width<600) {
            $('.outofstock-list').width('38px');
            $('.newproduct-list').width('38px');
            $('.saleproduct-list').width('38px');
        }
        else
        {
            $('.outofstock-list').width('80px');
            $('.newproduct-list').width('80px');
            $('.saleproduct-list').width('80px');
        }
        pos1 = $('#outofstock-position').html();
        label.onProductList(".outofstock-list", pos1,$('.product-image-wrapper').height());
        //New Product Label
        pos2 = $('#newproduct-position').html();
        label.onProductList(".newproduct-list", pos2,$('.product-image-wrapper').height());
        //Sale Product Label
        pos3 = $('#saleproduct-position').html();
        label.onProductList(".saleproduct-list", pos3,$('.product-image-wrapper').height());


        $(window).resize(function(){
            width = $(window).width();
            if(width<600) {
                $('.outofstock-list').width('38px');
                $('.newproduct-list').width('38px');
                $('.saleproduct-list').width('38px');
            }
            else
            {
                $('.outofstock-list').width('80px');
                $('.newproduct-list').width('80px');
                $('.saleproduct-list').width('80px');
            }
            //Out Of Stock Label
            pos1 = $('#outofstock-position').html();
            label.onProductList(".outofstock-list", pos1,$('.product-image-wrapper').height());
            //New Product Label
            pos2 = $('#newproduct-position').html();
            label.onProductList(".newproduct-list", pos2,$('.product-image-wrapper').height());
            //Sale Product Label
            pos3 = $('#saleproduct-position').html();
            label.onProductList(".saleproduct-list", pos3,$('.product-image-wrapper').height());
        });
    }
    else {
        var width=$('.product-image-photo').width();
        //Out Of Stock Label
        pos1 = $('#outofstock-position').html();
        label.onProductGrid(".outofstock-list", pos1);
        //New Product Label
        pos2 = $('#newproduct-position').html();
        label.onProductGrid(".newproduct-list", pos2);
        //Sale Product Label
        pos3 = $('#saleproduct-position').html();
        label.onProductGrid(".saleproduct-list", pos3);

        $(window).resize(function(){
            width=$('.product-image-photo').width();
            //Out Of Stock Label
            pos1 = $('#outofstock-position').html();
            label.onProductGrid(".outofstock-list", pos1,width-$('.outofstock-list').width());
            //New Product Label
            pos2 = $('#newproduct-position').html();
            label.onProductGrid(".newproduct-list", pos2,width-$('.newproduct-list').width());
            //Sale Product Label
            pos3 = $('#saleproduct-position').html();
            label.onProductGrid(".saleproduct-list", pos3,width-$('.saleproduct-list').width());
        });
    }
});