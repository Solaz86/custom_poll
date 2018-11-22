<?php
namespace Drupal\poll_form\Form;
 
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Link;
use Drupal\Core\Url;
 
 
class PollForm extends FormBase {
 
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    // Form name
    return 'custom_poll_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $form['message'] = [
      '#prefix' => '<div class="subscription-message">',
      '#markup' => '<p>Entro en formulario</p>',
      '#suffix' => '</div>',
    ];

    /*
    $form['cc'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Cédula'),
      '#size' => 15,
      '#maxlength' => 15,
      '#description' => 'Ingrese cédula',
      '#placeholder' => 'Cédula*',
      '#attributes' => array('id' => 'cc-suscription')
    );
    
    $form['submit'] = array(
      '#type'  => 'submit',
      '#value' => $this->t('Iniciar sesión'),
    );*/

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $mail = $form_state->getValue('mail');
    $pass = $form_state->getValue('pass');
    $cc = $form_state->getValue('cc');
    
    if (empty($mail) && !empty($pass)) {
      $form_state->setErrorByName('mail', $this->t('Es necesario diligenciar el campo email'));
    }

    if (!empty($mail) && empty($pass)) {
      $form_state->setErrorByName('pass', $this->t('Es necesario diligenciar el campo de contraseña'));
    }

    if (!empty($cc) && !is_numeric($cc)) {
      $form_state->setErrorByName('cc', $this->t('El campo cédula debe ser númerico'));
    }

    if (empty($mail) && empty($pass) && empty($cc)) {
      $form_state->setErrorByName('cc', $this->t('Debes llenar el campo cédula o los campos mail y contraseña'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $mail = $form_state->getValue('mail');
    $pass = $form_state->getValue('pass');
    $cc = $form_state->getValue('cc');

    $urlWs = 'http://34.215.185.38/CirculoExp/';
    $client = \Drupal::httpClient();
    $serv_hashed = \Drupal::service('password');

    if (!empty($mail) && !empty($pass)) {
      $hashed_pass = $serv_hashed->hash($pass);
      $request = $client->get($urlWs, [
        'query' => array(
          'email' => $mail,
          'passwd' => $hashed_pass
        )
      ]);
      $json_resp = $request->getBody()->getContents();
    }
    elseif (!empty($cc)) {
      $request = $client->get($urlWs, [
        'query' => array(
          'cedula' => $cc,
        )
      ]);
      $json_resp = $request->getBody()->getContents();
    }

    // Use web services
    $resp = json_decode($json_resp, true);
    if (!empty($resp) && isset($resp['status'])) {
      switch ($resp['status']) {

        // Usuario válido y contraseña OK
        case 1:
          if (user_load_by_mail($mail)) {
            $userLoad = user_load_by_mail($mail);
            user_login_finalize($userLoad);
          }
          else {
            $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
            $user = \Drupal\user\Entity\User::create();
            $user->setPassword('C1rcul0');
            $user->enforceIsNew();
            $user->setEmail($mail);
            $user->setUsername($mail);
            $user->set("init", $mail);
            $user->set("langcode", $language);
            $user->set("preferred_langcode", $language);
            $user->set("preferred_admin_langcode", $language);
            $user->activate();
            $user->addRole('suscriptor');
            $user->save();
            user_login_finalize($user);
          }
          drupal_set_message(t('Has iniciado sesión de forma correcta.'));
          break;

        // Suscriptor solo con cédula y válido
        case 2:
          $options = array(
            'attributes' => array(
              'target' => '_blank',
            ),
          );
          $link = Link::fromTextAndUrl(t('El espectador'), Url::fromUri('https://www.elespectador.com/', $options))->toString();
          drupal_set_message(t('Felicitaciones, Si tienes una cuenta pero debes registrarte en este portal @link', array('@link' => $link)));
          break;

        // No es un usuario valido ni por correo ni con cédula
        case 3:
          drupal_set_message(t('Lo sentimos no te encuentras registrado'), 'error');
          break;

        // Suscripción vencida
        case 4:
          drupal_set_message(t('Tu suscripción esta vencida.'), 'error');
          break;

        // Usuario válido - Contraseña Incorrecta
        case 5:
          drupal_set_message(t('Lo sentimos tu correo o contraseña no son validos.'), 'error');
          break;
        
        default:
          break;
      }
    }
    else {
      drupal_set_message(t('Lo sentimos en este momento tenemos inconvenientes con el login, por favor intenta mas tarde.'), 'error');
    }

  }
}