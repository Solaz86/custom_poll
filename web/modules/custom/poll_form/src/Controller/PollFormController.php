<?php
namespace Drupal\poll_form\Controller;
 
use Drupal;
use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\Entity\Paragraph;
 
class PollFormController extends ControllerBase {
   
  public function pollForm() {
    $poll_nid = $this->_poll_form_get_activate_poll();
    if ($poll_nid && is_numeric($poll_nid)) {
      $values = $this::_poll_form_get_paragraph_values($poll_nid);
      $topics_order = $this::_poll_form_get_order_topics();
      /*echo '<pre>';
      print_r($values);
      echo '</pre>';

      echo '<pre>';
      print_r($topics_order);
      echo '</pre>';*/
      if (!empty($values) && isset($values[$topics_order[0]]) && 
        isset($values[$topics_order[1]]) && isset($values[$topics_order[2]]) ) {
        // Call form
        $form = $this->formBuilder()->getForm('Drupal\poll_form\Form\PollForm', $values, $topics_order);

      }
    }

       
    // Le pasamos el formulario y demás a la vista (tema configurado en el module)
    return [
      '#theme' => 'pollForm',
      '#title' => $this->t('Poll'),
      //'#form' => 'hola',
      '#form' => $form
    ];
  }


  public function _poll_form_get_activate_poll() {
    $poll_nid = NULL;
    $query = \Drupal::database()->select('node', 'n');
    $query->addField('n', 'nid');
    $query->join('node__field_active_agile_survey', 'fa', 'n.nid = fa.entity_id');
    $query->condition('fa.bundle', 'poll');
    $query->condition('fa.field_active_agile_survey_value', 1);
    $result = $query->execute()->fetchField();
    if (!empty($result)) {
      $poll_nid = $result;
    }
    return $poll_nid;
  }


  public function _poll_form_get_paragraph_values($poll_nid) {
    $poll = \Drupal\node\Entity\Node::load($poll_nid);
    $values = array();
    if (!empty($poll)) {
      $paragraphs = $poll->field_question->getValue();
      // Loop through the result set.
      foreach ($paragraphs as $element) {
        $prgh = \Drupal\paragraphs\Entity\Paragraph::load($element['target_id']);
        
        if (!empty($prgh)) {
          $type_qustn = $prgh->type->getValue();
          switch ($type_qustn[0]['target_id']) {
            case 'question':
              $label_qustn = $prgh->field_tittle->getValue()[0]['value'];
              $topic_tid = $prgh->field_topic->getValue()[0]['target_id'];
              //$values[$topic_tid][]['label'] = $label_qustn;

              $anws = $prgh->field_option->getValue();
              $info_anws = array();
              
              if (!empty($anws)) {
                
                foreach ($anws as $key => $anw) {
                  $anws_load = \Drupal\paragraphs\Entity\Paragraph::load($anw['target_id']);
                  $punctuation = $anws_load->field_option_punctuation->getValue()[0]['value'];
                  $response = $anws_load->field_response_option->getValue()[0]['value'];
                  $info_anws[] = array(
                    'punt' => $punctuation,
                    'resp' => $response                    
                  );
                  /*$values[$topic_tid][]['answs'][] = array(
                    'punt' => $punctuation,
                    'resp' =>$response                    
                  );*/
                }
                $values[$topic_tid][] = array(
                  'label' => $label_qustn,
                  'answs' => $info_anws                   
                );
                
              }
              break;
            
            case 'open_question':
              break;
          }
        } 
      }
    }
    return $values;
  }

  public function _poll_form_get_order_topics(){
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    if (!empty($lang)) {
      switch ($lang) {
        case 'en':
          $topic_order = array();
          $topic_order[0] = 1;
          $topic_order[1] = 2;
          $topic_order[2] = 3;
          break;
        
        case 'es':
          # code...
          break;
      }
      return $topic_order;
    }
  }
   
}
 
?>