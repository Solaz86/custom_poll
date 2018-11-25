/**
 * @file
 * Suscriptor file.
 */

(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.poll_form = {
    attach: function (context) {
      
      if ($('body').hasClass('path-poll')) {
	      $('.form-checkbox').click(function(){
			    var id_check = $(this).attr("id");
			    var $checks = $(this).parents('.cnt-question').find("input[type='checkbox']");
			    
			    $checks.each(function(){
			    	if (id_check != $(this).prop('id')) {
			        $(this).prop('checked', false);
			    	}
			    });

				});
      }
    }
  }
}(jQuery, Drupal));
