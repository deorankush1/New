if(Validation) {       
  Validation.addAllThese([     
   [
        'validate-pincode-length',      
        'Pincode should be 6 digits',   
        function(v,r)
        {  

         return !(v.length < 6 || v.length > 6);  

        }
   ]
  
   ])
  setTimeout(function() {
            //jQuery('#message').hide();
            jQuery('.validation-advice').hide();
            }, 5000);
}

jQuery(function(){
    var pincode = document.querySelectorAll("#pin");
   //console.log(pincode);
   document.querySelector("#pin").addEventListener("keypress", function (e) {
  if (e.which < 48 || e.which > 57)
  {
      e.preventDefault();
  }
});
    jQuery("#pincode_form").submit(function(e){
        e.preventDefault();
       
    	jQuery('#message').html("");

        var pincode = jQuery('#pin').val();
        var urll = jQuery('#get_url').val();


        if (dataForm.validator.validate() ) {
            jQuery.ajax({

             url : urll+"/pincode/index/getPincodeByParam/pincode/"+pincode,
            dataType : "json",
            type : "GET",
           beforeSend: function() {
                  // setting a timeout
                 // jQuery().addClass('loading');
                //alert("here");
                  jQuery('#ajax-loader').show();
              },
            success : function(data)
            {
              jQuery('#ajax-loader').hide();
                console.log(data);
                if(data.pincode == pincode && data.status == 1)
                {
                    jQuery("#message").html("Available within 4-5 Working Days").css({"color":"red", "display":"block" });
                }
                else if(data.pincode == pincode && data.status == 0)
                {
                    jQuery("#message").html("Not Available").css({"color":"red", "display":"block" });   
                }
                else
                {
                    jQuery("#message").html("Delivery Not Available").css({"color":"red", "display":"block" });   
                }
                setTimeout(function() {
                                jQuery('#message').hide();
                                jQuery('.validation-advice').hide();
                            }, 5000);
               
            }
             });
      
        }
    });
});
