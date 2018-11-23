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
  public function buildForm(array $form, FormStateInterface $form_state, $values = array(), $topics_order = array()) {

    if (!empty($values) && !empty($topics_order)) {

      /*echo '<pre>';
      print_r(array_keys($values));
      echo '</pre>';*/

      /*unset($_SESSION['poll']);
      echo '<pre>';
      print_r($_SESSION);
      echo '</pre>';*/

      echo '<pre>';
      print_r('Sesion');
      echo '</pre>';
      echo '<pre>';
      print_r($_SESSION);
      echo '</pre>';

      /*$step = NULL;
      if (!isset($_SESSION['poll']['step'])) {
        $step = 1;
      }
      elseif (isset($_SESSION['poll']['step']) && $_SESSION['poll']['step'] == 2) {
        $step = 2;
      }
      elseif (isset($_SESSION['poll']['step']) && $_SESSION['poll']['step'] == 3) {
        $step = 3;
      }
      elseif (isset($_SESSION['poll']['step']) && $_SESSION['poll']['step'] == 4) {
        $step = 4;
      }
      //$form['step_hd'] = array('#type' => 'hidden', '#value' => $step);*/

      $storage = $form_state->getStorage();

      echo '<pre>';
      print_r('Sorage en form');
      echo '</pre>';
      echo '<pre>';
      print_r($storage);
      echo '</pre>';

      //kint($_SESSION);

      if (empty($storage)) {
        $step = 1;
        $storage = array('step' => $step);
        $form_state->setStorage($storage);
      }
      else {
        $step = $storage['step'];
      }

      echo '<pre>';
      print_r('Step en form');
      echo '</pre>';
      echo '<pre>';
      print_r($step);
      echo '</pre>';


      switch ($step) {
        case 1:
        case 2:
        case 3:
          $topic = $values[$topics_order[$step-1]];
          $topic_id = $topics_order[$step-1];
          /*echo '<pre>';
          print_r($topic);
          echo '</pre>';*/
          
          $form[$topic_id]['#tree'] = TRUE;
          $form['topic_id_hd'] = array('#type' => 'hidden', '#value' => $topic_id);          
          foreach ($topic as $key_quest => $quest) {
            $form[$topic_id][$key_quest]['label']['#markup'] = '<p>' . $quest['label'] . '</p>';
            foreach ($quest['answs'] as $key_answ => $answ) {
              $form[$topic_id][$key_quest]['answs']['resp_'.$key_answ] = array( 
                '#type' => 'checkbox',
                '#title' => $answ['resp'],
              );
              $form[$topic_id][$key_quest]['hd_resp_'.$key_answ] = array('#type' => 'hidden', '#value' => $answ['punt']);
            }            
          }          
          break;

        case 4:
          $form['complete_name'] = array(
            '#type' => 'textfield', 
            '#title' => $this->t('Name'), 
            '#size' => 60, 
            '#maxlength' => 120, 
            '#required' => TRUE,
          );
          $form['mail'] = array(
            '#type' => 'textfield', 
            '#title' => $this->t('Mail'), 
            '#size' => 60, 
            '#maxlength' => 120, 
            '#required' => TRUE,
          );
          break;

      }
      
      
      /*$storage = $form_state->getStorage();
      if (empty($storage)) {
        $storage = array('step' => 1);
      }
      echo '<pre>';
      print_r('en form');
      echo '</pre>';
      echo '<pre>';
      print_r($storage);
      echo '</pre>';
      

      $form_state->setStorage($storage);*/

      $form['submit'] = array(
        '#type'  => 'submit',
        '#value' => $this->t('Next'),
      );


      return $form;
    }
    
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /*$step = $form_state->getValue('step_hd');
    $values = $form_state->getValues();
    kint($step);
    if ($step < 4) {
      kint('entro a topics');
      $topic_id = $form_state->getValue('topic_id_hd');
      $arr_values_step = $values[$topic_id];
      $cont_quest = $acum_quest = $prom_topic = 0;
      foreach ($arr_values_step as $key_arr_step => $arr_step) {
        foreach ($arr_step as $key_step => $val_step) {
          if ($key_step == 'answs') {
            foreach ($val_step as $key_answ => $answ) {
              if ($answ) {
                $acum_quest += $arr_step['hd_'.$key_answ];
                $cont_quest++;
              } 
            }

          }
          
        }
      }

      $prom_topic = $acum_quest / $cont_quest; 
      $_SESSION['poll']['topics'][] = array(
        'topic_id' => $topic_id,
        'prom_topic' => $prom_topic,
      );
      if (is_numeric($step)) {
        $_SESSION['poll']['step'] = $step + 1;
      }
    }
    else {
      kint('entro a datos basicos');

      $_SESSION['poll']['user_info'] = array(
        'complete_name' => $form_state->getValue('complete_name'),
        'mail' => $form_state->getValue('mail'),
      );
      if (!empty($_SESSION['poll']['topics'])) {
        $cont_tot_prom = 0;
        foreach ($_SESSION['poll']['topics'] as $topic) {
          $cont_tot_prom += $topic['prom_topic'];
        }
        $_SESSION['poll']['tot_prom'] = $cont_tot_prom/3;
      }
    }



    
    $form_state->setErrorByName('1', $this->t('Es necesario diligenciar el campo email'));
    /*$mail = $form_state->getValue('mail');
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
    }*/
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    //$step = $form_state->getValue('step_hd');
    

    /*$storage = $form_state->getStorage();
    echo '<pre>';
    print_r('en submit antes');
    echo '</pre>';
    echo '<pre>';
    print_r($storage);
    echo '</pre>';
    $step = $storage['step'];

    echo '<pre>';
    print_r('step');
    print_r($step);
    echo '</pre>';

    $storage['step'] = $step + 1;
    $form_state->setStorage($storage);
    $form_state->setRebuild();
    echo '<pre>';
    print_r('en submit despues');
    echo '</pre>';
    echo '<pre>';
    print_r($storage);
    echo '</pre>';*/

    $storage = $form_state->getStorage();
    $values = $form_state->getValues();
    kint('submit');
    kint($values);
    kint($storage);
    $step = $storage['step'];


    
    if ($step < 4) {
      
      $topic_id = $form_state->getValue('topic_id_hd');
      $arr_values_step = $values[$topic_id];
      $cont_quest = $acum_quest = $prom_topic = 0;
      foreach ($arr_values_step as $key_arr_step => $arr_step) {
        foreach ($arr_step as $key_step => $val_step) {
          if ($key_step == 'answs') {
            foreach ($val_step as $key_answ => $answ) {
              if ($answ) {
                $acum_quest += $arr_step['hd_'.$key_answ];
                $cont_quest++;
              } 
            }

          }
          
        }
      }

      $prom_topic = $acum_quest / $cont_quest; 
      $_SESSION['poll']['topics'][] = array(
        'topic_id' => $topic_id,
        'prom_topic' => $prom_topic,
      );
      /*if (is_numeric($step)) {
        $_SESSION['poll']['step'] = $step + 1;
      }*/
    }
    else {

      $_SESSION['poll']['user_info'] = array(
        'complete_name' => $form_state->getValue('complete_name'),
        'mail' => $form_state->getValue('mail'),
      );
      if (!empty($_SESSION['poll']['topics'])) {
        $cont_tot_prom = 0;
        foreach ($_SESSION['poll']['topics'] as $topic) {
          $cont_tot_prom += $topic['prom_topic'];
        }
        $_SESSION['poll']['tot_prom'] = $cont_tot_prom/3;
      }
    }

    
    $storage['step'] = $step + 1;
    $form_state->setStorage($storage);
    $form_state->setRebuild();

    

  }
}