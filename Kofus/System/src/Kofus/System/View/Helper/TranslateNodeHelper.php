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
    
    public function __invoke(\Kofus\System\Node\NodeInterface $node, $options=array())
    {
        $locale = null;
        if (is_string($options)) {
            $locale = $options;
            $this->options = array();
        } elseif (is_array($options)) {
            if (isset($options['locale'])) $locale = $options['locale'];
            $this->options = $options;
        }
        
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
        $value = $this->getTranslationService()->translateNode($this->node, $name, $arguments, $this->locale);
        if (! $value && isset($this->options['fallback'])) {
            $locale = $this->getServiceLocator()->get('KofusConfig')->get('locales.default', 'de_DE');
            $value = $this->getTranslationService()->translateNode($this->node, $name, $arguments, $locale);
        }
        return $value;
            
    }
    
    
}


