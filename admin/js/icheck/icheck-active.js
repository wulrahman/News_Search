(function ($) {
 "use strict";
 
  $('.i-checks').iCheck({
		checkboxClass: 'icheckbox_square-green',
		radioClass: 'iradio_square-green',
	});

    $('.i-checks').on('ifChecked', function (event){
        $(this).closest("input").attr('checked', true);          
    });
    $('.i-checks').on('ifUnchecked', function (event) {
        $(this).closest("input").attr('checked', false);
    });

	
      $('.i-checks1').iCheck({
		checkboxClass: 'icheckbox_square-green',
		radioClass: 'iradio_square-green',
	});

    $('.i-checks1').on('ifChecked', function (event){
        $(this).closest("input").attr('checked', true);          
    });
    $('.i-checks1').on('ifUnchecked', function (event) {
        $(this).closest("input").attr('checked', false);
    });

      $('.i-checks2').iCheck({
		checkboxClass: 'icheckbox_square-green',
		radioClass: 'iradio_square-green',
	});

    $('.i-checks2').on('ifChecked', function (event){
        $(this).closest("input").attr('checked', true);          
    });
    $('.i-checks2').on('ifUnchecked', function (event) {
        $(this).closest("input").attr('checked', false);
    });

	
 
})(jQuery); 