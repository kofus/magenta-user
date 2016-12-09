<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class DatabaseController extends AbstractActionController
{
    public function upgradeAction()
    {
    	$classNames = $this->em()->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

    	/*
    	$classNames = array(
    			'Kofus\System\Entity\LinkEntity', 
		    	'Kofus\System\Entity\NodeTranslationEntity',
    			'Kofus\System\Entity\RelationEntity'
    	);
    	
    	foreach ($this->config()->get('nodes.enabled') as $nodeType) {
    		$classname = $this->config()->get('nodes.available.' . $nodeType . '.entity');
    		if ($classname)
    			$classNames[] = $classname;
    	} */
    	
    	//print_r($classNames); die();
    	$tool = new \Doctrine\ORM\Tools\SchemaTool($this->em());
    	$metadata = array();
    	foreach ($classNames as $className)
    		$metadata[] = $this->em()->getClassMetadata($className);
    	$tool->updateSchema($metadata);
    	
    	return new ViewModel(array(
    	    'classnames' => $classNames
    	));
    }
    
    public function backupAction()
    {
        $backup = $this->getServiceLocator()->get('KofusDatabase');
        $backup->download();
    }
    
    
}
