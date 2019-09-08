<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class TranslateNodeHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $sm;
    protected $node;
    protected $translationService;
    
    public function __invoke(\Kofus\System\Node\NodeInterface $node, $locale=null)
    {
        $this->node = $node;
        $this->locale = $locale;
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
    
    protected function getTranslationService()
    {
        if (! $this->translationService)
            $this->translationService = $this->getServiceLocator()->get('KofusTranslationService');
        return $this->translationService;
    }
    
    public function __call($name, $arguments)
    {
        return $this->getTranslationService()->translateNode($this->node, $name, $arguments, $this->locale);
    }
    
    
}


