<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class TranslateLinkHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $sm;
    protected $link;
    
    public function __invoke($locale, $uri=null)
    {
        if (! $uri)
            $uri = $_SERVER['REQUEST_URI'];
        $link = $this->em()->createQueryBuilder()
            ->select('l')
            ->from('Kofus\System\Entity\LinkEntity', 'l')
            ->where('l.uri = :uri')
            ->setParameter('uri', $uri)
            ->getQuery()->getOneOrNullResult();
        
        $tLink = null;
        if ($link) {
            $tLink = $this->em()->getRepository('Kofus\System\Entity\LinkEntity')->findOneBy(array(
                'locale' => $locale,
                'linkedNodeId' => $link->getLinkedNodeId(),
                'context' => $link->getContext()            	
            ));
        }
        
        if (! $tLink) {
            $routeMatch = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();
            if ($routeMatch) {
                $language = substr($locale, 0, 2);
                $tLink = $this->getView()->url(null, array('language' => $language), true);
            }
        }
        
        return $tLink;
    	
    }
    
    protected function em()
    {
        return $this->getServiceLocator()->get('Doctrine\Orm\EntityManager');
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
    	$this->sm = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->sm->getServiceLocator();
    }
    
    
    
}


