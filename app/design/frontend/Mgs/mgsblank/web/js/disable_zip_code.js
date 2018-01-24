require(['jquery', 'jquery/ui'], function($){ 

  $(document).ready(function(){

    $( ".country #country" ).change(function() {

      if($(this).val()=="AE" || $(this).val()=="ae" ){
        $(".zip #zip").attr("disabled","");
      }
      else{
        $(".zip #zip").removeAttr("disabled");
      }
      });


    //for checkout fields

    $("body").on("change","select[name='country_id']", function(){

      if($( "[name='country_id']" ).val()=="AE" || $( "[name='country_id']" ).val()=="ae" ){
        $("[name='postcode']").attr("disabled","");
      }
      else{
        $("[name='postcode']").removeAttr("disabled");
      }
      });


  });

});