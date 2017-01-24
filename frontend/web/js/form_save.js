/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

     
$(document).on("beforeSubmit", "#add-form", function () {
    // send data by ajax request.
    var form = $(this);
    try{
       $.ajax({
            url    : form.attr('action'),
            type   : 'post',
            data   : form.serialize(),
            success: function (data) 
            {
                console.log(data);
                comment(data);
            },
            error  : function () 
            {
                console.log('internal server error');
            }
            });
   
    return false; // Cancel form submitting.
} catch(err){ alert('Error data send');}
});

     
     
$(document).on("beforeSubmit", "#form-blockchain-submit", function () {
    // send data by ajax request.
    var form = $(this);
    $('.loader_head').show();
    
    try{
       $.ajax({
            url    : form.attr('action'),
            type   : 'post',
            data   : form.serialize(),
            success: function (data) 
            {
                if (data == false){
                    alert('ouups, some error. Try again later..');
                } else {
                $('#modalsignupBlockchain').modal('hide');
                $('#GOLOS').val(data);
                $('#golos-btn-save').click();
            };
            
            $('.loader_head').hide();
    
            },
            error  : function () 
            {
                console.log('internal server error');
            }
            });
   
    return false; // Cancel form submitting.
} catch(err){ alert('Error data send');}
});
