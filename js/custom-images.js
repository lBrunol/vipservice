/*
 * Classe auxiliar que permite adicionar novos campos de imagem a posts e custom types
 * Author: Darren Krape
 * Edited by: Angelo Santos/Foster
 */
 
jQuery(document).ready(function($){

	var cpi_image_frame = wp.media({
		title: 'Selecione ou envie uma nova imagem',
		button: { text: 'Selecionar imagem' }
	});

	cpi_image_frame.on('select', function() {
		var media_attachment = cpi_image_frame.state().get('selection').first().toJSON(),
			media_id = media_attachment.id,
			media_thumbnail = media_attachment.sizes.full.url;

		p = $(cpi_image_frame.currentElement.closest('.cpi-upload'));
		
		p.addClass('active');
		p.find('.cpi-upload-id').val(media_id);
		p.find('.cpi-upload-thumbnail').html('<img src="' + media_thumbnail + '">');
	});

	$('body').on('click', '.cpi-upload-button', function (e) {
		e.preventDefault();

		var p = $(this).closest('.cpi-upload');

		cpi_image_frame.open();
		cpi_image_frame.currentElement = this;
	});

	$('body').on('click', '.cpi-upload-clear', function (e) {
		e.preventDefault();
			
		var p = $(this).closest('.cpi-upload');

        p.removeClass('active');
		p.find('.cpi-upload-id').val('');
		p.find('.cpi-upload-thumbnail').empty();
	});

	$('body').on('click', '.cpi-upload-thumbnail', function (e) {
		var p = $(this).closest('.cpi-upload');

		if (p.hasClass('active') && $('.fs-preview-img').length == 0)
            $('body').append('<div class="fs-preview-img active"><a href="#" title="Fechar" id="fecharPreview">&times</a><div class="fs-container"></div></div>');
        else if (p.hasClass('active'))
            $('.fs-preview-img').addClass('active');
        
        if (p.hasClass('active'))
            $('.fs-preview-img .fs-container').html('<img src="' + $(this).find('img').attr('src') + '" />');
	});

    $('#cpi .cpi-upload, .container-cpi .cpi-upload').closest('form').find('input[type=submit]').on('click', function(e) {
		var p = $(this).closest('form').find('.cpi-upload');

        setTimeout(function () {
            if (p.closest('form').find('.form-invalid').length == 0) {
                p.removeClass('active');
                p.find('.cpi-upload-id').val('');
                p.find('.cpi-upload-thumbnail').empty();
            }
        }, 500);
	});

    $('body').on('click', '#fecharPreview', function (e) {
        e.preventDefault();
        $('.fs-preview-img').removeClass('active');
    });
    
    $(document).on('keydown', function (e) {
        if (e.keyCode == 27 && $('.fs-preview-img').length > 0)
            $('.fs-preview-img').removeClass('active');
    });
});