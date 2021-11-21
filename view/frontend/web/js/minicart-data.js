// Ready function
require(['jquery'], function ($) {
    $(document).ready(function (e) {
		$(document).on('click', '.minicart-wrapper .addProduct:not(.disabled)', function(e) {
			e.preventDefault();
			var formele = $(this).closest("form");
			var keyval = formele.find('input').val();
			if(!keyval){
				return false;
			}
			$(this).addClass('disabled');
			var formData = new FormData();    
			formData.append( 'form_key', formele.find('input').val());
			$.ajax({
                url: formele.attr('action'),
                data: formData,
                type: 'post',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (res) {
					setTimeout(function(){
						if(!$('.minicart-wrapper').hasClass('active')){
							$('[data-block="minicart"]').find('[data-role="dropdownDialog"]').dropdownDialog("open");
						}
					},3000);
					
					$(this).removeClass('disabled');
                },

                /** @inheritdoc */
                error: function (res) {
                    $(this).removeClass('disabled');
                },

                /** @inheritdoc */
                complete: function (res) {
                    $(this).removeClass('disabled');
                }
            });
			return false;
		});
	})
})