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
}

jQuery(function(){
    var pincode = document.querySelectorAll("#pin");
   console.log(pincode);
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
            success : function(data)
            {
                console.log(data);
                if(data.pincode == pincode && data.status == 1)
                {
                    jQuery("#message").html("Available");
                }
                else if(data.pincode == pincode && data.status == 0)
                {
                    jQuery("#message").html("Not Available");   
                }
                else
                {
                    jQuery("#message").html("Pincode Not Available in db");   
                }
               
            } });
      
        }
    });
});
