<?php

namespace Kofus\System\I18n\Translator\Loader;


use Zend\I18n\Exception;
use Zend\I18n\Translator\TextDomain;
use Zend\I18n\Translator\Loader\RemoteLoaderInterface;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class Nodes implements RemoteLoaderInterface
{
    protected $sm;
    
    public function __construct($sm)
    {
        $this->sm = $sm;
    }
    
    public function load($locale, $textDomain)
    {
        $em = $this->sm->get('Doctrine\ORM\EntityManager');
        $entities = $em->getRepository('Kofus\System\Entity\NodeTranslationEntity')->findBy(array(
            'locale' => $locale, 
            'textDomain' => $textDomain)
        );
        
        $messages = array();
        foreach ($entities as $entity)
            $messages[$entity->getMsgId()] = $entity->getValue();
        
        return new TextDomain($messages);
    }
    
}
