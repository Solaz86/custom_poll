<?php

namespace Drupal\poll_graph\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\taxonomy\Entity\Term;


/**
 * Class PollgraphController
 * @package Drupal\poll_graph\Controller
 */
class PollgraphController extends ControllerBase {


  /**
   * Generate data required to generate the poll stats graphic
   * @return mixed
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  function getPollgraph() {
    $title = $this->t('Poll graph implementation');

    $build['myelement'] = [
      '#theme' => 'poll_graph_page',
      '#title' => $title,
    ];
    //    check $_SESSION graph data
    if(isset($_SESSION['graph']) && !empty($_SESSION['graph'])) {

      $poll_session = $_SESSION['graph'];
      $max = [];
      $notas = [];
      //      set each topic average and max grade
      foreach ($poll_session['topics'] as $key => $value) {
        $term = Term::load($value['topic_id']);
        $topic = $term->getName();
        $max[$topic] = $value['max_value'];
        $notas['id' . $value['topic_id']] = $value['prom_topic'];
      }
      //      set total average grade
      $notas['average'] = $poll_session['tot_average'];
      //      set the user data
      $name = $poll_session['user_info']['complete_name'];
      $email = $poll_session['user_info']['mail'];
      $user_exist = user_load_by_mail($email);
      //      create the user
      if (!$user_exist){
        $this->createUser($name, $email);
      }

    }
    //    attach the library
    $build['myelement']['#attached']['library'][] = 'poll_graph/poll_graph.library';
    //    send $notas through drupalSettings to Javascript
    $build['#attached']['drupalSettings']['poll_graph']['stats'] = $notas;
    //    clean $_SESSION topics
    unset($poll_session['topics']);
    return $build;
  }


  /**
   * Create the user
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  function createUser($name, $email) {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $user = \Drupal\user\Entity\User::create();

    //    Mandatory settings
    $user->setPassword('password');
    $user->enforceIsNew();
    $user->setEmail($email);
    $user->setUsername($name);
    $user->addRole('poll_user');

    //    Optional settings
    $user->set("init", 'email');
    $user->set("langcode", $language);
    $user->set("preferred_langcode", $language);
    $user->set("preferred_admin_langcode", $language);
    //$user->set("setting_name", 'setting_value');

    //$user->activate();

    //    Save user
    $res = $user->save();

    return array();

  }
}
