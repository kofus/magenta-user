<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class DoctrineController extends AbstractActionController
{
    public function listAction()
    {
        $this->archive()->uriStack()->push();
        
        $connections = array();
        foreach ($this->config()->get('doctrine.connection') as $label => $data)
            $connections[] = $label;
        return new ViewModel(array(
            'connections' => $connections
        ));
    }
    
    public function upgradeAction()
    {
        $connectionId = $this->params('id');
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.' . $connectionId);
        $metadata = array();
        
        $config = $this->config()->get('doctrine.driver.' . $connectionId . '.drivers');
        foreach ($config as $classPrefix => $configKey) {
            $paths = $this->config()->get('doctrine.driver.' . $configKey . '.paths');
            foreach ($paths as $path) {
                foreach (scandir($path) as $filename) {
                    if (in_array($filename, array('.', '..'))) continue;
                    $classname = preg_replace('/\.php$/', '', $filename);
                    //print $classPrefix . '\\' . $classname . '<br>';
                    $metadata[] = $em->getClassMetadata($classPrefix . '\\' . $classname);
                }
            }
        }
        
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $tool->updateSchema($metadata);
            
        return $this->redirect()->toUrl($this->archive()->uriStack()->pop());
    }
    
    public function dumpAction()
    {
        $connectionId = $this->params('id');
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.' . $connectionId);
        $path = 'data/doctrine/backups';
        $filename = date('Y-m-d-H-i-s') . '-' . $em->getConnection()->getDatabase() . '.sql';
        if (! file_exists($path)) {
            $success = mkdir($path, 0777, true);
            if (! $success)
                throw new \Exception('Could not create directory ' . $path);
        }
        
        
        $command = 'mysqldump --skip-set-charset ';
        $command .= '-r ' . escapeshellarg($path . '/' . $filename) . ' ';
        $command .= '--user=' . escapeshellarg($em->getConnection()->getUsername()) . ' ';
        $command .= '--password=' . escapeshellarg($em->getConnection()->getPassword()) . ' ';
        $command .= '--host=' . escapeshellarg($em->getConnection()->getHost()) . ' ';
        $command .= '--port=' . escapeshellarg($em->getConnection()->getPort()) . ' ';
        $command .= escapeshellarg($em->getConnection()->getDatabase());
        
        exec($command);
        
        $response = new \Zend\Http\Response\Stream();
        $response->setStream(fopen($path . '/' . $filename, 'r'));
        $response->setStatusCode(200);
        
        $headers = new \Zend\Http\Headers();
        $headers->addHeaderLine('Content-Type', 'application/sql; charset=utf-8')
        ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->addHeaderLine('Content-Length', filesize($path . '/' . $filename));
        
        $response->setHeaders($headers);
        return $response;    
    }
    
    protected function createUploadForm()
    {
        $form = new \Zend\Form\Form();
        
        $file = new \Zend\Form\Element\File('file');
        $file->setLabel('MySql File')->setAttribute('id', 'file');
        $form->add($file);
        
        $form->add(new \Zend\Form\Element\Submit('submit', array('label' => 'Submit')));
        
        return $form;
    }
    
    protected function runNativeSql($sql, $connectionId)
    {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.' . $connectionId);
        $db = new \Zend\Db\Adapter\Adapter(array(
             'driver'       => 'Pdo_Mysql',
             'host'         => $em->getConnection()->getHost(),
             'database'     => $em->getConnection()->getDatabase(),
             'username'     => $em->getConnection()->getUsername(),
             'password'     => $em->getConnection()->getPassword(),
             'port'         => $em->getConnection()->getPort()
         ));

        $db->query($sql)->execute(); 
    }
    
    public function uploadAction()
    {
        $connectionId = $this->params('id');
        
        $form = $this->createUploadForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            // Make certain to merge the files info!
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            
            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();
                $s = file_get_contents($data['file']['tmp_name']);
                $this->runNativeSql($s, $connectionId);
                return $this->redirect()->toUrl($this->archive()->uriStack()->pop());
 
            }
        }
        
        return new ViewModel(array(  
            'connectionId' => $connectionId,
            'form' => $form->prepare()
        ));
    }
    
    
}