<?php

namespace Drupal\poll_graph\Controller;

use Drupal\Core\Controller\ControllerBase;


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


        $build['myelement']['#attached']['library'][] = 'poll_graph/poll_graph.library';
//        $build['myelement']['#attached']['drupalSettings']['poll_graph']['stats'] = $notas;
    return $build;
  }
}
