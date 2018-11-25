/**
 * @file
 * Suscriptor file.
 */

(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.poll_form = {
    attach: function (context) {
    	console.log('hola desde js form');
      //var $form = $('#custom-login-form');
      $('.custom-poll-form input[type="checkbox"]').click(function(){
		    //console.log('click checkbox');
		    var $checks = $(this).parents('.cnt-question').find("input[type='checkbox']");
		    
		    $checks.each(function(){
		        //$(this).checked = false;
		        //console.log('check');
		    });

			});
      
    }
  }
}(jQuery, Drupal));
