$(document).ready(function () {
	$( "body" )
	.delegate(".showModule","click", function(e){$("."+$(this).data("target")).slideDown( "slow");$([document.documentElement, document.body]).animate({scrollTop: $("."+$(this).data("target")).offset().top}, 1000);})
	.delegate(".hideModule","click", function(e){$("."+$(this).data("target")).slideUp( "slow");})
	.delegate(".addmorefile","click",function(e){
		var accepts = $(this).data('types');
    	if(accepts=='')
    		accepts='image/*';
    	$(this).parents('.controls').find('.fileswrapper').append('<input  name="filesToUpload[]" type="file" accept="'+accepts+'">');
	});


	$('[data-toggle="tooltip"]').tooltip(); 
	if ($(window).width() <= 760) {$([document.documentElement, document.body]).animate({scrollTop: $(".content").offset().top}, 1000);}
});

var popUpWin=0;
function popUpWindow(URLStr, left, top, width, height)
{
	if(popUpWin)
	{
		if(!popUpWin.closed) 
			popUpWin.close();
	}
	popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+600+',height='+600+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}

//----------------------Scripts By Ahsan Danish------------------------
function formatFieldName(fieldName) {
    let words;
    if (fieldName.includes('_')) {
        words = fieldName.split('_');
    } else {
        words = [fieldName];
    }
    let capitalizedWords = words.map(word => word.charAt(0).toUpperCase() + word.slice(1));
    let formattedName = capitalizedWords.join(' ');
    return formattedName;
}
function createErrorComponent(targetElement, errorMessage) {
    // Create the main error container
    let errorContainer = document.createElement('div');
    errorContainer.className = 'errorMessage';
    errorContainer.textContent = errorMessage;
    // Append the error container to the target element
    targetElement.insertAdjacentElement('afterend', errorContainer);
}
function removeErrorComponent(targetElement) {
    let errorDiv = targetElement.nextElementSibling;
    if (errorDiv && errorDiv.classList.contains('errorMessage')) {
        errorDiv.remove();
    }
}
