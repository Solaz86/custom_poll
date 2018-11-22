<?php
namespace Drupal\poll_form\Controller;
 
use Drupal;
use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;
 
class PollFormController extends ControllerBase {
   
  public function pollForm() {
    // Utilizamos el formulario
    $form = $this->formBuilder()->getForm('Drupal\poll_form\Form\PollForm');
       
    // Le pasamos el formulario y demás a la vista (tema configurado en el module)
    return [
      '#theme' => 'pollForm',
      '#title' => $this->t('Poll'),
      '#form' => $form
    ];
  }
   
}
 
?>