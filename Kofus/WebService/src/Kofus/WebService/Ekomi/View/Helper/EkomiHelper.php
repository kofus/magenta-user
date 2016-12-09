<?php

namespace Kofus\WebService\Ekomi\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class EkomiHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $sm;
    
    public function __invoke()
    {
        return $this;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
    	$this->sm = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->sm->getServiceLocator();
    }
    
    public function __toString()
    {
        return $this->render();
    }
    
    protected $certId;
    
    public function getCertId()
    {
        if (! $this->certId)
            $this->certId = $this->getServiceLocator()->get('KofusConfig')->get('webservice.ekomi.cert_id');
        return $this->certId;
    }
    
    public function hasCertId()
    {
        return (bool) $this->getCertId();
    }
    
    public function render()
    {
        if (! $this->getCertId())
            return '';
        
        $this->getView()->headScript()->appendScript("
        		eKomiIntegrationConfig = new Array(
        		{certId:'".$this->getCertId()."'}
							);
        				if(typeof eKomiIntegrationConfig != 'undefined'){
        				for(var eKomiIntegrationLoop=0;eKomiIntegrationLoop<eKomiIntegrationConfig.length;eKomiIntegrationLoop++){
        				var eKomiIntegrationContainer = document.createElement('script');
        				eKomiIntegrationContainer.type = 'text/javascript'; eKomiIntegrationContainer.defer = true;
        				eKomiIntegrationContainer.src = (document.location.protocol=='https:'?'https:':'http:') +'//connect.ekomi.de/integration_1400676204/' + eKomiIntegrationConfig[eKomiIntegrationLoop].certId + '.js';
        				document.getElementsByTagName('head')[0].appendChild(eKomiIntegrationContainer);
        				}}else{if('console' in window){ console.error('connectEkomiIntegration - Cannot read eKomiIntegrationConfig'); }}
            ");
        return '<div id="eKomiWidget_default"></div>';
        
        
    }
    
}


