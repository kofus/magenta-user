<?php
namespace Kofus\System\Mvc;
use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ErrorRoute implements RouteInterface, ServiceLocatorAwareInterface
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
        return array('controller' => 'error');
    }
        
    public static function factory($options = array())
    {
         if ($options instanceof \Traversable) {
                $options = ArrayUtils::iteratorToArray($options);
            } elseif (!is_array($options)) {
                throw new InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
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
    }
    
}