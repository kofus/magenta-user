<?php

namespace Kofus\User\Search\Document;
use Kofus\User\Entity\AccountEntity;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Document;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountDocument extends Document implements ServiceLocatorAwareInterface
{
	public function populateNode(AccountEntity $node)
	{
		$this->addField(
				Field::text('node_id', $node->getNodeId())
		);
		$this->addField(
				Field::text('node_type', $node->getNodeType())
		);
		
		$this->addField(Field::text('label', $node));
		$this->addField(Field::text('comment', $node->getRole() . ', ' . $node->getStatus(true)));
		
		$nodes = $this->getServiceLocator()->get('KofusNodeService');
		$auths = $nodes->getRepository('AUTH')->findBy(array('account' => $node));
		foreach ($auths as $index => $auth) 
		    $this->addField(Field::text('auth_identity' . $index, $auth->getIdentity()));
	}
	
	protected $sm;
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->sm = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->sm;
	}
	
}