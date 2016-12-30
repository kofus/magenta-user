<?php
namespace Kofus\System\Mvc;
use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PublicFilesRoute implements RouteInterface, ServiceLocatorAwareInterface
{
    protected $defaults = array();
    protected $route;
    protected $pm;
    
    public function __construct($route, array $defaults = array())
    {
    	$this->route    = $route;
    	$this->defaults = $defaults;
    }
    
    public function setServiceLocator(ServiceLocatorInterface $sm) 
    {
    	$this->pm = $sm;
    }
    
    public function getServiceLocator() 
    {
    	return $this->pm;
    }    
    
    public function getAssembledParams()
    {
        return array();
    }
        
    public static function factory($options = array())
    {
         if ($options instanceof \Traversable) {
                $options = \ArrayUtils::iteratorToArray($options);
            } elseif (!is_array($options)) {
                throw new \InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
            }
        
            if (!isset($options['defaults'])) {
                $options['defaults'] = array();
            }

    return new static($options['defaults']);    }
    
    /**
     * @param  Request $request
     * @return RouteMatch|null
     */
    public function match(Request $request, $pathOffset=null)
    {
        $uri = $request->getUri()->getPath();
        
        // Consider only uris with lib prefix
        $service = $this->getServiceLocator()->get('KofusPublicFilesService');
        if (strpos($uri, $service->getUriPathPrefix()) !== 0)
            return;

        $filename = $service->getFilenameByUri($uri);
        if ($filename) {
            $options = array(
            		'__NAMESPACE__'     => 'Kofus\System\Controller',
            		'controller'        => 'public-files',
            		'filename'          => $filename,
            		'action'            => 'dump'
            );
            return new RouteMatch($options);
        }
    }
    
    /**
     * Assemble the route.
     *
     * @param  array $params
     * @param  array $options
     * @return mixed
    */
    public function assemble(array $params = array(), array $options = array())
    {
        if (array_key_exists('path', $params)) {
        	return '/' . $params['path'];
        }
        
        return '/';
    }
    
}