<?php

namespace Kofus\System\Service;

use Kofus\System\Service\AbstractService;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface as Router;

class NavigationService extends AbstractService
{
    protected $navbar;
    
    public function setNavbar($value)
    {
        $this->navbar = $value; return $this;
    }
    
    public function getNavbar()
    {
        return $this->navbar;
    }
    
    protected $excludeDisabledPages = true;
    
    public function excludeDisabledPages($bool=null)
    {
        if ($bool !== null) {
            $this->excludeDisabledPages = (bool) $bool;
            return $this;
        }
        return $this->excludeDisabledPages;
    }
    
    protected $excludeInvisiblePages = true;
    
    public function excludeInvisiblePages($bool=null)
    {
    	if ($bool !== null) {
    		$this->excludeInvisiblePages = (bool) $bool;
    		return $this;
    	}
    	return $this->excludeInvisiblePages;
    }
    
    protected $isSystemNavbar = false;
    
    public function isSystemNavbar($bool=null)
    {
    	if ($bool !== null) {
    		$this->isSystemNavbar = (bool) $bool;
    		return $this;
    	}
    	return $this->isSystemNavbar;
    }
    
    
    protected $uriPrefix;
    
    public function setUriPrefix($value)
    {
        $this->uriPrefix = $value; return $this;
    }
    
    public function getUriPrefix($resolveTokens = true)
    {
            return $this->uriPrefix;
    }
    
    protected function applyUriSubstitutions($uri)
    {
        $uri = trim($uri, '/');
        if ($this->getUriPrefix())
            $uri = trim($this->getUriPrefix(), '/') . '/' . $uri;
        
        foreach ($this->getUriSubstitutions() as $key => $value)
            $uri = str_replace($key, $value, $uri);
        return '/' . $uri;
    }
    
    protected $uriSubstitutions = array();
    
    public function setUriSubstitution($key, $value)
    {
        $this->uriSubstitutions[$key] = $value;
        return $this;
    }
    
    public function getUriSubstitution($key)
    {
        if (isset($this->uriSubstitutions[$key]))
            return $this->uriSubstitutions[$key];
    }
    
    public function getUriSubstitutions()
    {
        if (! isset($this->uriSubstitutions['{language}']))
            $this->uriSubstitutions['{language}'] = $this->getServiceLocator()->get('KofusLocale')->getLanguage();
        return $this->uriSubstitutions;
    }
    
    protected $config = array();
    
    public function loadConfig()
    {
        $this->config = $this->config()->get('navbars.' . $this->getNavbar());
        if (! $this->config)
            throw new \Exception('No configuration found for navbar ' . $this->getNavbar());
        if (array_key_exists('uri_prefix', $this->config))
            $this->setUriPrefix($this->config['uri_prefix']);
        if (array_key_exists('exclude_disabled_pages', $this->config))
            $this->excludeDisabledPages($this->config['exclude_disabled_pages']);
        if (array_key_exists('exclude_invisible_pages', $this->config))
        	$this->excludeInvisiblePages($this->config['exclude_invisible_pages']);
    }

    /**
     * @param string $name
     * @return \Zend\Navigation\Navigation
     */
    public function getContainer($options=array())
    {
        $container = new \Zend\Navigation\Navigation($this->getArray($options));
        $this->injectHelperClasses($container);
        return $container;
    }
    
    public function injectHelperClasses(\Zend\Navigation\Navigation &$nav)
    {
        if (! $nav->findOneBy('active', 1)) {
            $pages = $nav->findAllBy('uri', $_SERVER['REQUEST_URI']);
            foreach ($pages as $page)
                $page->setActive();
        }
        
    }
    
    public function createNavPage($entity)
    {
        $link = $this->nodes()->getLink($entity);
        $navPage = array(
        		'uri' => (string) $link, 
        		'label' => $this->getTranslationService()->translateNode($entity, 'getNavLabel'),
        		'enabled' => $entity->isNavVisible(),
        		'node-id' => $entity->getNodeId(),
                'order' => $entity->getPriority(),
                'resource' => $entity->getNodeType(),
                'privilege' => 'view'
        );
        return $navPage;
        
	}
	
	protected function preparePages($pages)
	{
		$application = $this->getServiceLocator()->get('Application');
		$routeMatch  = $application->getMvcEvent()->getRouteMatch();
		$router      = $application->getMvcEvent()->getRouter();
	
		return $this->injectComponents($pages, $routeMatch, $router);
	}
	
	protected function injectComponents(array $pages, RouteMatch $routeMatch = null, Router $router = null)
	{
		foreach ($pages as &$page) {
			$hasMvc = isset($page['action']) || isset($page['controller']) || isset($page['route']);
			if ($hasMvc) {
			    
				if (!isset($page['routeMatch']) && $routeMatch) {
					$page['routeMatch'] = $routeMatch;
				}
				if (!isset($page['router'])) {
					$page['router'] = $router;
				}
			}
	
			if (isset($page['pages'])) {
				$page['pages'] = $this->injectComponents($page['pages'], $routeMatch, $router);
			}
		}
		return $pages;
	}
	
	
    protected function getArrayLocales(array $options=array())
    {
		$application = $this->getServiceLocator()->get('Application');
        $routeMatch  = $application->getMvcEvent()->getRouteMatch();
        $links = $this->getServiceLocator()->get('KofusLinkService');
        
        $pages = array();
        foreach ($this->getServiceLocator()->get('KofusConfig')->get('locales.enabled', array()) as $locale) {
            if (\Locale::getDefault() == $locale)
                continue;
            
            $uri = null;
            $language = substr($locale, 0, 2);
            if ($routeMatch && $routeMatch->getParam('node')) {
                $tLink = $links->getTranslationLink($routeMatch->getParam('node'), $locale); 
                if ($tLink)
                    $uri = $tLink->getUri();
            } elseif($routeMatch) {
                
                $viewHelperManager = $this->getServiceLocator()->get('viewHelperManager');
                $urlHelper = $viewHelperManager->get('url');
                $route = $routeMatch->getMatchedRouteName();
                $params = $routeMatch->getParams();
                $params['language'] = $language;
                $params['locale'] = $locale;
                if (isset($params['__CONTROLLER__'])) {
                    $params['controller'] = $params['__CONTROLLER__'];
                    unset($params['__CONTROLLER__']);
                }
                $uri = $urlHelper($route, $params, array(), null);
            }
            
            if (! $uri)
                $uri = $language;
            
            $page = array(
                'uri' => '/' . trim($uri, '/'),
                'label' => \Locale::getDisplayLanguage($locale, $locale),
            );
            $pages[] = $page;
        }
        return $pages;
    }
    
    
    public function getArray(array $options = array())
    {
    	$userService = $this->getServiceLocator()->get('KofusUserService');
    	$cache = $this->getServiceLocator()->get('Cache');
    	$hashParams = array(\Locale::getDefault(), $userService->getRole(), $this->getNavbar());
    	$hash = 'nav.' . md5(implode(',', $hashParams));
    	if (true || ! $cache->hasItem($hash)) {
	        $pages = array();
	        
	        
	        // Special method for this navbar?
	        $method = 'getArray' . $this->getNavbar();
	        if (method_exists($this, $method))
	            $pages = array_merge_recursive($pages, $this->$method());
	        
	        
	        $qb = $this->em()->createQueryBuilder()
	            ->select('e')
	            ->from('Kofus\System\Entity\PageEntity', 'e')
	            ->where('e.navbar = :navbar')
	            ->setParameter('navbar', $this->getNavbar())
	            ->andWhere('e.parent IS NULL')
	            ->orderBy('e.priority');
	        
	        if ($this->excludeInvisiblePages())
	            $qb->andWhere('e.navVisible = true');
	        if ($this->excludeDisabledPages())
	            $qb->andWhere('e.enabled = true');
	            
	        $entities = $qb->getQuery()->getResult();
	        
	        foreach ($entities as $entity) {
	        	$navPage = $this->createNavPage($entity);
	        	if ($navPage) {
	        	    $navPage['uri'] = '/' . trim($navPage['uri'], '/');
	        		$children = $this->getChildren($entity);
	        		if ($children)
	        			$navPage['pages'] = $children;
	        		$pages[$entity->getNodeId()] = $navPage;
	        	}
	        }
	        
	        // 2. Is there a config spec?
	        $config = $this->sm->get('KofusConfig');
	        if ($config->get('navigation.' . $this->getNavbar())) {
	            $configPages = $config->get('navigation.' . $this->getNavbar());
	            
	            // HACK
	            foreach ($configPages as &$configPage) {
	                $configPage['params'] = array('language' => \Locale::getPrimaryLanguage(\Locale::getDefault()));
	            }
	            $pages = array_merge_recursive($pages, $configPages);
	            $cache->setItem($hash, serialize($pages));
	        }
    	} else {
    		$pages = unserialize($cache->getItem($hash));
    	}    	

        $pages = $this->preparePages($pages);
        
        return $pages;
        
    }
    
    protected function getChildren($parent)
    {
    	$em = $this->sm->get('Doctrine\ORM\EntityManager');
    	$qb = $em->createQueryBuilder()
        	->select('e')
        	->from('Kofus\System\Entity\PageEntity', 'e')
        	->where('e.parent = :parent')
        	->setParameter('parent', $parent)
        	->orderBy('e.priority');
    	
    	if ($this->excludeInvisiblePages())
    		$qb->andWhere('e.navVisible = true');
    	if ($this->excludeDisabledPages())
    		$qb->andWhere('e.enabled = true');
    	
        $entities = $qb->getQuery()->getResult();
    
    	$pages = array();
    	foreach ($entities as $child) {
    		$navPage = $this->createNavPage($child);
    		if ($navPage) {
    		    $navPage['uri'] = '/' . trim($navPage['uri'], '/');
    			$rChildren = $this->getChildren($child);
    			if ($rChildren)
    				$navPage['pages'] = $rChildren;
    			$pages[$child->getNodeId()] = $navPage;
    		}
    	}
    
    	return $pages;
    }
    
    protected $translationService;
    
    protected function getTranslationService()
    {
        if (! $this->translationService)
            $this->translationService = $this->getServiceLocator()->get('KofusTranslationService');
        return $this->translationService;
    }
    
    
}