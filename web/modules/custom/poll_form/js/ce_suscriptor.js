/**
 * @file
 * Suscriptor file.
 */

(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.ce_suscriptor = {
    attach: function (context) {
      var $form = $('#custom-login-form');

			$form.find("#edit-submit").click(function(event) {
		    event.preventDefault();
		    var mail = $('#email-suscription').val();
		    var pass = $('#pass-suscription').val();
		    var cc = $('#cc-suscription').val();
		    var $msg = '';
		    var caract = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);

    		if (mail.length > 0 && caract.test(mail) == false) {
    			$msg = '<p class="alert alert-danger">El correo ingresado no es valido</p>';
    		}
		    else if (pass.length <= 0 && mail.length > 0) {
		    	$msg = '<p class="alert alert-danger">Es necesario diligenciar el campo contraseña</p>';
		    }
		    else if (pass.length > 0 && mail.length <= 0) {
		    	$msg = '<p class="alert alert-danger">Es necesario diligenciar el campo email</p>';
		    }
		    else if (cc.length > 0 && isNaN(cc)) {
		      $msg = '<p class="alert alert-danger">El campo cédula debe ser númerico</p>';
		    }
		    else if (pass.length <= 0 && mail.length <= 0 && cc.length <= 0) {
		    	$msg = '<p class="alert alert-danger">Por favor ingrese un correo electrónico y contraseña o una cédula si aún no se ha registrado</p>';
		    }
		    else {
		    	$form.submit();
		    }
		    $('.subscription-message').html($msg);
  
			});
      
    }
  }
}(jQuery, Drupal));
