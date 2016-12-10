<?php
namespace Kofus\User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        
        $entities = $this->nodes()->createQueryBuilder('U');
        $paginator = $this->paginator($entities, array(
        	'sort_columns' => array(
        		'id' => 'n.id',
        		'role' => 'n.role',
        		'name' => 'n.name',
        		'date_created' => 'n.timestampCreated',
        		'date_login' => 'n.timestampLogin'
        	),
        	'default_sort_directions' => array(
        		'date_login' => 'DESC'
        )
        ));
        return new ViewModel(array(
        	'paginator' => $paginator
        ));
    }
    
    public function loginasAction()
    {
        
    }
}
