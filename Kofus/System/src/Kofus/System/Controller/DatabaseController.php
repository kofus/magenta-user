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
        $dbService = $this->getServiceLocator()->get('KofusDatabase');
        
        $path = 'data/backups/';
        $filename = $dbService->createFilename();
        $dbService->save($path . $filename);
        
        $response = new \Zend\Http\Response\Stream();
        $response->setStream(fopen($path . $filename, 'r'));
        $response->setStatusCode(200);
        
        $headers = new \Zend\Http\Headers();
        $headers->addHeaderLine('Content-Type', 'application/sql; charset=utf-8')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->addHeaderLine('Content-Length', filesize($path . $filename));
        
        $response->setHeaders($headers);
        return $response;        
        
    }
    
    
}