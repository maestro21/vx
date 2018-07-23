
$( document ).ready(function() {
		
	


});


function tab(i) {
	$('body').addClass('test');
	$('.tabMenu div').removeClass();
	$('.tabMenu div[data-id=' + i + ']').addClass('active');
	$('.tabs .tab').hide();
	$('.tabs .tab[data-id=' + i + ']').show();
}


function bindForm() {
	$('input[type="file"]').on('change', function() {
	  var val = $(this).val().replace(/^.*\\/, "");
	  $(this).siblings('span').text(val);
	})
	$('.delfile').click(function() { 
		var data = {
			file: $(this).siblings('a').attr('href')
		};
		console.log(data);
		//$(this).parentNode.html('<?php echo T("no files selected");?>');
		$.post('<?php echo BASE_URL . 'fileviewer/delbyurl';?>?ajax=1',data);
	});
	
}

function sendForm(form,path) {
    if(form == null) return;
    if(!form.valid()) return;
    if(path == null) path = form.attr("action");    
	syncEditorContents();

	var data = $(form).find('input, select, textarea').not('[name*="{key}"]').serializefiles();
    console.log(data);

    // add files
    $.each( $(form).find('input[type="file"]'), function( key, value ) {
       data.append(value.id, value.files[0]);
    });

	$.ajax({
		url: path,
		data: data,
		cache: false,
		contentType: false,
		processData: false,
		type: 'POST',
		success: function(data){
			processResponse(data);
		}
	});
}

function sendFormById(id,path) {
	if(id == null) id = 'form';
    if(!$('#' + id)) return;
    sendForm($('#' + id),path);
}


function processResponse(data, form) {
	data = jQuery.parseJSON(data); console.log(data);
	$('.messages').html('');
	$('.messages').hide();
	addmsg(data.message, data.status);	
	$('.messages').show(300);
	if(data.status == 'ok') {
		var timeout = 2000;
		if(data.timeout) timeout = data.timeout;
		setTimeout(function() {
			if(data.redirect) {
				if(data.redirect == 'self' || data.redirect == 'reload') 
					window.location.reload();
				else 
					window.location = data.redirect;
			}
			$('.messages').html('');
		},timeout);
	}	
}

function sendGetForm(id,path) {
	$.get(path, $('#' + id).serialize())
	.done(function( data ) {
		processResponse(data);		
	});
}


function conf(action, text) {
	if(confirm(text)){
		$.get(action + '?ajax=1')
		.done(function( data ) {
			processResponse(data);
		});
	}
}


function addmsg(txt, cl, selector) {
	if(selector == null) selector = '.messages';
	if(cl == null) cl = 'ok';
	var html = '<div class="' + cl + '">' + txt + '</div>';
	$(selector).html($(selector).html() + html);
}


/* Editor editor */
function syncEditorContents() {
	 $('textarea').each(function() {
        var id = $(this).attr("id");
		if($(this).hasClass('html')) {
			$(this).appendTo('form');
			$(this).val(tinyMCE.get(id).getContent());
		}
    });
}


