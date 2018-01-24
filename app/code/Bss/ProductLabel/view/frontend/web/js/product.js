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
    $('.columns').css('position','relative');
    var width = $(window).width();
    var height_img,width_img;
    $(document).on('gallery:loaded', function () {
        height_img=$('.gallery-placeholder').height();
        width_img=$('.gallery-placeholder').width();
        if (width < 768){
            //Out Of Stock Label
            label.productOnMobile(".outofstock-label",pos1,height_img,$('.outofstock-label').height());

            //New Product Label
            label.productOnMobile(".newproduct-label",pos2,height_img,$('.newproduct-label').height());

            //Sale Product Label
            label.productOnMobile(".saleproduct-label",pos3,height_img,$('.saleproduct-label').height());
        }
        else {
            //Out Of Stock Label
            label.productOnWindow(".outofstock-label",pos1,width_img-$('.outofstock-label').width()-50,height_img,$('.outofstock-label').height()+20);

            //New Product Label
            label.productOnWindow(".newproduct-label",pos2,width_img-$('.newproduct-label').width()-50,height_img,$('.newproduct-label').height()+20);

            //Sale Product Label
            label.productOnWindow(".saleproduct-label",pos3,width_img-$('.saleproduct-label').width()-50,height_img,$('.saleproduct-label').height()+20);
        }
    });
    var pos1=$('#outofstock-position').html();
    var pos2=$('#newproduct-position').html();
    var pos3=$('#saleproduct-position').html();

    $(window).resize(function(){
        width = $(window).width();
        height_img=$('.gallery-placeholder').height();
        width_img=$('.gallery-placeholder').width();
        if (width < 768){
            //Out Of Stock Label
            label.productOnMobile(".outofstock-label",pos1,height_img,$('.outofstock-label').height());

            //New Product Label
            label.productOnMobile(".newproduct-label",pos2,height_img,$('.newproduct-label').height());

            //Sale Product Label
            label.productOnMobile(".saleproduct-label",pos3,height_img,$('.saleproduct-label').height());
        }
        else{
            //Out of stock label
            label.productOnWindow(".outofstock-label",pos1,width_img-$('.outofstock-label').width()-50,height_img,$('.outofstock-label').height()+20);

            //New Product Label
            label.productOnWindow(".newproduct-label",pos2,width_img-$('.newproduct-label').width()-50,height_img,$('.newproduct-label').height()+20);

            //Sale Product Label
            label.productOnWindow(".saleproduct-label",pos3,width_img-$('.saleproduct-label').width()-50,height_img,$('.saleproduct-label').height()+20);
        }
    });
});