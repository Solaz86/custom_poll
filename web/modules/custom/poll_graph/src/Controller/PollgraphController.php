<?php

namespace Drupal\poll_graph\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Messenger\MessengerInterface;


class PollgraphController extends ControllerBase {


  function getPollgraph() {
    $title = $this->t('Poll graph implementation');


    $build['myelement'] = [
      '#theme' => 'poll_graph_page',
      '#title' => $title,
    ];
    $notas = [
      'tecnologia' => 80,
      'innovacion' => 60,
      'habilidades' => 30,
      'promedio' => 50,
    ];

    if(!isset($_SESSION['poll']))
    {
      $_SESSION['poll'] = array();

    }
    $poll_session = & $_SESSION['poll'];

    $poll_session['tecnologia'] = 40;
    $poll_session['innovacion'] = 50;
    $poll_session['habilidades'] = 100;
    $poll_session['promedio'] = 25;


    $notas = [
      'tecnologia' => $poll_session['tecnologia'],
      'innovacion' => $poll_session['innovacion'],
      'habilidades' => $poll_session['habilidades'],
      'promedio' => $poll_session['promedio'],
    ];


        $build['myelement']['#attached']['library'][] = 'poll_graph/poll_graph.library';
        $build['#attached']['drupalSettings']['poll_graph']['stats'] = $notas;
    return $build;
  }


  /**
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  function createUser() {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $user = \Drupal\user\Entity\User::create();

//Mandatory settings
    $user->setPassword('password');
    $user->enforceIsNew();
    $user->setEmail('email');
    $user->setUsername('user_name1');//This username must be unique and accept only a-Z,0-9, - _ @ .

//Optional settings
    $user->set("init", 'email');
    $user->set("langcode", $language);
    $user->set("preferred_langcode", $language);
    $user->set("preferred_admin_langcode", $language);
    //$user->set("setting_name", 'setting_value');
    $user->activate();

//Save user
    $res = $user->save();
//    drupal_set_message(t('My message after redirect'), 'status', TRUE);
//    $mesage = MessengerInterface();

    return array();

  }
}
