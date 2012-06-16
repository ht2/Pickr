// JavaScript Document
$(document).ready( commonInit );

function commonInit()
{
	$('textarea, input[type=text]').focus( textareaFocus ).blur( checkEmptyTextarea ).each( checkEmptyTextarea );
}

function checkEmptyTextarea()
{
	if( $(this).val() == "" || $(this).val() == $(this).attr('title') ) $(this).val( $(this).attr('title') ).addClass('grey');
	else $(this).removeClass('grey');
}

function textareaFocus()
{
	if( $(this).val() == $(this).attr('title') ) $(this).val('');
	$(this).removeClass('grey');
}
