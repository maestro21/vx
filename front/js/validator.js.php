<?php die(); ?>

(function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		define( ["jquery", "jquery.validate"], factory );
	} else if (typeof module === "object" && module.exports) {
		module.exports = factory( require( "jquery" ) );
	} else {
		factory( jQuery );
	}
}(function( $ ) {

/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: RU (Russian; русский язык)
 */
$.extend( $.validator.messages, {
	phone: "Пожалуйста, введите правильный номер телефона",
	required: "Пожалуйста, заполните это поле",
	remote: "Пожалуйста, введите правильное значение.",
	email: "Пожалуйста, введите корректный адрес электронной почты.",
	url: "Пожалуйста, введите корректный URL.",
	date: "Пожалуйста, введите корректную дату.",
	dateISO: "Пожалуйста, введите корректную дату в формате ISO.",
	number: "Пожалуйста, введите число.",
	digits: "Пожалуйста, вводите только цифры.",
	creditcard: "Пожалуйста, введите правильный номер кредитной карты.",
	equalTo: "Пожалуйста, введите такое же значение ещё раз.",
	extension: "Пожалуйста, выберите файл с правильным расширением.",
	maxlength: $.validator.format( "Пожалуйста, введите не больше {0} символов." ),
	minlength: $.validator.format( "Пожалуйста, введите не меньше {0} символов." ),
	rangelength: $.validator.format( "Пожалуйста, введите значение длиной от {0} до {1} символов." ),
	range: $.validator.format( "Пожалуйста, введите число от {0} до {1}." ),
	max: $.validator.format( "Пожалуйста, введите число, меньшее или равное {0}." ),
	min: $.validator.format( "Пожалуйста, введите число, большее или равное {0}." )
} );

}));


$().ready(function() {
	
	$.validator.addMethod('phone', function (value, element) {
		return this.optional(element) || /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im.test(value);
	});
});

function bindvalidate(selector) {
	$(selector).validate({
		
		rules: {			
            phone: {
				required: true,
				phone: true
			},
        },
		
		submitHandler: function(form) {
			$('#errorMsg').hide();
			$('#saveMsg').show(500);
			setTimeout(function() {	$('#saveMsg').hide() }, 3000);
			$.post( $(selector).attr( "action" ), $(selector).serialize(), function(data) {
				$('#saveMsg').show(500);
				setTimeout(function() {	$('#saveMsg').hide() }, 5000);
			}, "json");
		},
		
		invalidHandler: function(event, validator) {
        console.
			$('#errorMsg').show(500);
		}
		
	});
}
