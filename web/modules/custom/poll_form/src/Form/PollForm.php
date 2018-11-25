<?php
namespace Drupal\poll_form\Form;
 
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Link;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use \Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use \Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\RedirectResponse;


 
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
  public function buildForm(array $form, FormStateInterface $form_state, $values = array(), $topics_order = array(), $poll_nid = NULL) {
    if (!empty($values) && !empty($topics_order)) {

//      unset($_SESSION['poll']);
      /*echo 'POLL';
      echo '<pre>';
      print_r($_SESSION);
      echo '</pre>';*/

      $storage = $form_state->getStorage();
      if (empty($storage)) {
        $step = 1;
        $storage = array('step' => $step);
        $form_state->setStorage($storage);
      }
      else {
        $step = $storage['step'];
      }

      /*echo '<pre>';
      print_r('Step en form');
      echo '</pre>';
      echo '<pre>';
      print_r($step);
      echo '</pre>';*/


      switch ($step) {
        case 1:
        case 2:
        case 3:
          $topic = $values[$topics_order[$step-1]];
          $topic_id = $topics_order[$step-1];
          //echo '<pre>';
          //print_r($topic);
          //echo '</pre>';
          
          $form[$topic_id]['#tree'] = TRUE;
          $form['topic_id_hd'] = array('#type' => 'hidden', '#value' => $topic_id);          

          foreach ($topic as $key_quest => $quest) {
            $form[$topic_id][$key_quest] = array(
              '#prefix' => '<div class="cnt-question">',
              '#suffix' => '</div>'
            );
            //$form[$topic_id][$key_quest]['label']['#markup'] = '<p><h3>' . $quest['label'] . '</h3></p>';
            $form[$topic_id][$key_quest]['label'] = array(
              '#prefix' => '<div>',
              '#suffix' => '</div>',
              '#markup' => '<strong>' . $quest['label'] . '</strong>',
            );
            $form[$topic_id][$key_quest]['label_quest'] = array(
              '#type' => 'hidden', 
              '#value' => $quest['label']
            );
            foreach ($quest['answs'] as $key_answ => $answ) {
              $form[$topic_id][$key_quest]['answs']['resp_'.$key_answ] = array( 
                '#type' => 'checkbox',
                '#title' => $answ['resp'],
              );
              $form[$topic_id][$key_quest]['hd_punt_resp_'.$key_answ] = array(
                '#type' => 'hidden',
                '#value' => $answ['punt']
              );
              $form[$topic_id][$key_quest]['hd_lab_resp_'.$key_answ] = array(
                '#type' => 'hidden', 
                '#value' => $answ['resp']
              );
            }            
          }          
          break;

        case 4:
          $form['poll_nid_hd'] = array('#type' => 'hidden', '#value' => $poll_nid);
          $form['complete_name'] = array(
            '#type' => 'textfield', 
            '#title' => $this->t('Name'), 
            '#size' => 60,
            '#maxlength' => 120, 
            '#required' => TRUE,
          );
          $form['mail'] = array(
            '#type' => 'email', 
            '#title' => $this->t('Mail'), 
            '#size' => 60, 
            '#maxlength' => 120, 
            '#required' => TRUE,
          );
          break;
      }

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
    $values = $form_state->getValues();*/
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    

    $storage = $form_state->getStorage();
    $values = $form_state->getValues();
    //kint('submit');
    //kint($values);
    $step = $storage['step'];
    
    if ($step < 4) {
      
      $topic_id = $form_state->getValue('topic_id_hd');      

      /*echo '<pre>';
      print_r('Sesion poll');
      echo '</pre>';
      echo '<pre>';
      var_dump($_SESSION['poll']);
      echo '</pre>';*/

      $arr_values_step = $values[$topic_id];
      $cont_quest = $acum_quest = $prom_topic = 0;
      $arr_max_value = array();
      $arr_labels_answ = array();
      

      foreach ($arr_values_step as $key_arr_step => $arr_step) {
        foreach ($arr_step as $key_step => $val_step) {
          if ($key_step == 'answs') {
            $cont_quest++;
            foreach ($arr_step['answs'] as $key_answ => $answ) {
              if ($answ) {
                $last_char = substr($key_answ, -1);
                $acum_quest += $arr_step['hd_punt_resp_'.$last_char];
                $arr_labels_answ[] = array(
                  'label_quest' => $arr_step['label_quest'],
                  'label_answ' => $arr_step['hd_lab_resp_'.$last_char],
                  'punt' => $arr_step['hd_punt_resp_'.$last_char],
                );
              }
              $arr_max_value[] = $arr_step['hd_punt_resp_'.$last_char];
            }

          }
          
        }
      }

      $prom_topic = $acum_quest / $cont_quest; 
      $_SESSION['poll']['topics'][] = array(
        'topic_id' => $topic_id,
        'prom_topic' => $prom_topic,
        'max_value' => max($arr_max_value),
        'arr_labels_answ' => $arr_labels_answ
      );
    }
    else {
      $poll_nid = $form_state->getValue('poll_nid_hd');
      $_SESSION['poll']['user_info'] = array(
        'complete_name' => $form_state->getValue('complete_name'),
        'mail' => $form_state->getValue('mail'),
      );
      if (!empty($_SESSION['poll']['topics'])) {
        $cont_tot_prom = 0;
        foreach ($_SESSION['poll']['topics'] as $topic) {
          $cont_tot_prom += $topic['prom_topic'];
        }
        $_SESSION['poll']['tot_average'] = $cont_tot_prom/3;
      }

      // Paragrah topics
      $parag_topics_ids = array();
      foreach($_SESSION['poll']['topics'] as $key_topic => $topic) {
        $topic_id = $topic['topic_id'];
        $paragh_topic_score = Paragraph::create([
          'type' => 'topic_score',
          'field_score' => array(
            'value'  =>  $topic['prom_topic'],
          ),
          'field_topic_score' => array(
            'target_id'  =>  $topic['topic_id'],
          ),
        ]);
        $paragh_topic_score->save();
        $paragh_id =  $paragh_topic_score->id();
        $paragh_rid = $paragh_topic_score->getRevisionId();
        $parag_topics_ids[] = array(
          'id' => $paragh_id,
          'revision_id' => $paragh_rid
        );
      }
      
      
      // Paragrah question results
      $parag_quest_resul_ids = array();
      foreach($_SESSION['poll']['topics'] as $key_topics => $info_topic) {
        foreach($info_topic['arr_labels_answ'] as $key_labels => $info_labels) {

          $parag_quest_results = Paragraph::create([
            'type' => 'question_results',
            'field_question_label' => array(
              'value'  =>  $info_labels['label_quest'],
            ),
            'field_label_answer' => array(
              'value'  =>  $info_labels['label_answ'],
            ),
            'field_score_answer' => array(
              'value'  =>  $info_labels['punt'],
            ),
            'field_poll_results_reference' => array(
              'target_id'  =>  $poll_nid,
            ),
          ]);
          $parag_quest_results->save();
          $paragh_id =  $parag_quest_results->id();
          $paragh_rid = $parag_quest_results->getRevisionId();
          $parag_quest_resul_ids[] = array(
            'target_id' => $paragh_id,
            'target_revision_id' => $paragh_rid
          );
        }
      }

      $pool_results = Node::create([
        'type'  => 'poll_results',
        'title' => t('Poll results') . ' - ' . $_SESSION['poll']['user_info']['complete_name'],
        'field_user_name' => [
          'value' => $_SESSION['poll']['user_info']['complete_name'],
        ],
        'field_user_email' => [
          'value' => $_SESSION['poll']['user_info']['mail'],
        ],
        'field_average_score' => [
          'value' => $_SESSION['poll']['tot_average'],
        ],
        'field_poll_reference' => [
          'target_id' => $poll_nid,
        ],
        'field_score_topic_1' => [
          'target_id' => $parag_topics_ids[0]['id'],
          'target_revision_id' => $parag_topics_ids[0]['revision_id'],
        ],
        'field_score_topic_2' => [
          'target_id' => $parag_topics_ids[1]['id'],
          'target_revision_id' => $parag_topics_ids[1]['revision_id'],
        ],
        'field_score_topic_3' => [
          'target_id' => $parag_topics_ids[2]['id'],
          'target_revision_id' => $parag_topics_ids[2]['revision_id'],
        ],
        'field_question_result' => $parag_quest_resul_ids
      ]);
      $pool_results->save();
//      Redireccion a la grafica de estadisticas
      $response = new RedirectResponse('/poll-graph');
      $response->send();

      if (isset($_SESSION['poll']) && !empty($_SESSION['poll'])) {
        $_SESSION['graph'] = $_SESSION['poll'];
        unset($_SESSION['poll']);
      }

    }

    $storage['step'] = $step + 1;
    $form_state->setStorage($storage);
    $form_state->setRebuild();

  }
}
