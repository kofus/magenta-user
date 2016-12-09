<?php
namespace Kofus\User\Permissions\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl;

class Acl extends ZendAcl
{
	public function __construct($sm)
	{
		$this->sm = $sm;
	}
	
	public function isAllowed($role = null, $resource = null, $privilege = null)
	{
	    $callbacks = $this->sm->get('KofusConfig')->get('user.acl.callbacks');
	    if ($callbacks) { 
    		foreach ($callbacks as $rule) {
    			if (
    				($rule[0] == $role || $rule[0] === null)
    				&& ($rule[1] == $resource || $rule[1] === null)
    				&& ($rule[2] == $privilege || $rule[2] === null) 
    			) {
    				return $rule[3]($this->sm);
    			}
    		}
	    }
		return parent::isAllowed($role, $resource, $privilege);
	}
}