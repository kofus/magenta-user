<?php
namespace Kofus\System\Cron;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Kofus\System\Cron\CronInterface;
use Zend\Mail;
use Zend\Mime;

abstract class AbstractCron implements CronInterface, ServiceLocatorAwareInterface
{
    protected $spec = array();
    
    public function setSpecification(array $spec)
    {
    	$this->spec = $spec; return $this;
    }
    
    public function getSpecification()
    {
        return $this->spec;
    }
    
    
    protected $storeParams = array();
    
    public function getStoreParams()
    {
    	return $this->storeParams;
    }
    
    public function setStoreParams(array $params)
    {
    	$this->storeParams = $params; return $this;
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
    
    protected function sendDebugMail($txt, $subject='Cron Debug', $to='log@kofus.de')
    {
    	$html = new Mime\Part($txt);
    	$html->type = 'text/plain';
    	
    	$mimeBody = new Mime\Message();
    	$mimeBody->setParts(array($html));
    	
    	$mail = new Mail\Message();
    	$mail->setSubject('[' . $_SERVER['HTTP_HOST'] . '] ' . $subject);
    	$mail->addTo($to);
    	$mail->setBody($mimeBody);
    	$mail->setFrom('log@kofus.de', 'Cron Debug');
    	
    	$transport = new Mail\Transport\Sendmail();
    	$transport->send($mail);    	
    }
    
    
}