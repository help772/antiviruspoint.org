( function( $ ) {
		"use strict";

$('.my-form .add-box').on('click',function(){
    
        var n = $('.text-box').length + 1;
        var box_html = $('<p class="text-box"><label for="box' + n + '">Sidebar-<span class="box-number">' + n + '</span></label> <input type="text" name="hexwp_boxes[]" value="" id="box' + n + '" /> <a href="#" class="remove-box">Remove</a></p>');
        box_html.hide();
        $('#append').append(box_html);
        box_html.fadeIn('slow');
		
        return false;
		
		
    });	
		
$('.my-form').on('click', '.remove-box', function(){
    $(this).parent().css( 'background-color', '#FF6C6C' );
    $(this).parent().fadeOut("slow", function() {
        $(this).remove();
        $('.box-number').each(function(index){
            $(this).text( index + 1 );
        });
    });
    return false;
});
		


$('.clsSubmit').on('click',function(){
var m = $('.text-box').length;
if(m==0){alert("press OK to Reset");}
});

		
} )( jQuery );