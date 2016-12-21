<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class DatabaseController extends AbstractActionController
{
    public function upgradeAction()
    {
        $backup = $this->getServiceLocator()->get('KofusDatabase');
        $backup->save();
        
    	$classNames = $this->em()->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

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
