<?php

namespace Kofus\User\Service;

use Kofus\System\Service\AbstractService;

use Kofus\User\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

// This is a factory not a service !!!

class AclService extends AbstractService
{
    protected $acl;
    
    public function getRoles()
    {
        $roles = array();
        
        // Read roles from config
        foreach ($this->config()->get('user.acl.roles', array()) as $role => $parents) {
        	if (! is_string($role)) {
        		$role = $parents;
        		$parents = null;
        	}
        	$roles[$role] = $parents;
        }
        return $roles;
    }
    
    public function getResources()
    {
        $resources = array();
        
        // Read resources from config
        foreach ($this->config()->get('user.acl.resources', array()) as $resource => $parents) {
        	if (! is_string($resource)) {
        		$resource = $parents;
        		$parents = null;
        	}
        	$resources[$resource] = $parents;
        }
        
        // Add enabled node types
        foreach ($this->config()->get('nodes.enabled', array()) as $nodeType) {
            if (! isset($resources[$nodeType]))
                $resources[$nodeType] = null;
        }
        
        // Add relations
        foreach ($this->config()->get('relations.enabled', array()) as $relation) {
        	if (! isset($resources[$relation]))
        		$resources[$relation] = null;
        }
        
        // Controller mappings
        $mappings = $this->config()->get('user.controller_mappings');
        foreach ($mappings as $key => $value)
        	$resources[$key] = $value;
        
        // Nodes
        $enabledNodes = $this->config()->get('nodes.enabled');
        $nodes = $this->config()->get('nodes.available');
        foreach ($enabledNodes as $nodeType) {
        	$nodeConfig = $nodes[$nodeType];
        	if (isset($nodeConfig['controllers'])) {
        		foreach ($nodeConfig['controllers'] as $controller)
        			$resources[$controller] = $nodeType;
        	}
        }
        
        return $resources;
    }
    
    public function getAllowRules()
    {
        $rules = $this->config()->get('user.acl.rules.allow', array());
        
        return $rules;
    }
    
    public function getDenyRules()
    {
    	return $this->config()->get('user.acl.rules.deny', array());
    }
    
    
	public function getAcl()
	{
	    if (! $this->acl) {
	        $this->acl = new Acl($this->getServiceLocator());

	        foreach ($this->getRoles() as $role => $parents)
	           $this->acl->addRole(new Role($role), $parents);
	         
	        foreach ($this->getResources() as $resource => $parents)
	           $this->acl->addResource(new Resource($resource), $parents);
	         
            foreach ($this->getAllowRules() as $rule)
                $this->acl->allow($rule[0], $rule[1], $rule[2]);
            
            foreach ($this->getDenyRules() as $rule)
            	$this->acl->deny($rule[0], $rule[1], $rule[2]);
            
	    }
	    return $this->acl;
	}
	
	
}