<?php

namespace Mailer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;


class SubscriptionController extends AbstractActionController
{
    public function dispatch(Request $request, Response $response=null)
    {
    	parent::dispatch($request, $response);
    	$this->layout('layout/admin');
    }
    
    public function listAction()
    {
        $this->uriStack()->push();
    	$entities = $this->em()->getRepository('Mailer\Entity\SubscriptionEntity')->findAll();
    	return array(
    		'entities' => $entities
    	);
    }
}
