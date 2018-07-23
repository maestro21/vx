/* modal */

$( document ).ready(function() {
    $('.modal-close').click(function() {
		closeModal();
	});
});

function closeModal(reload) {
	$('#modal').hide();
	$('.modal-overlay').hide();
	if(reload) window.location.reload();
}
function showModal(data) {
	$('#modal .modal-body').html(data);
	$('#modal').show();
	$('.modal-overlay').show();
}

function modal(path,params) {
	$.post(path, params)
	.done(function( data ) {
		showModal(data)
	});
}

function eModal(el) {
	var data = $(el).html();
	showModal(data)
}
