<?php

namespace Kofus\User\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class UserHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $sl;
    

    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
    	$this->sl = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->sl;
    }
    
    protected function user()
    {
        return $this->getServiceLocator()->getServiceLocator()->get('KofusUserService');
    }
    
    public function __call($name, $arguments)
    {
        $userService = $this->user();
        
        return call_user_func_array(array($userService, $name), $arguments);

    }
    
    public function enableAutologout()
    {
        $this->user()->triggerAutologoutHeartbeat();
        
        $urlAutologout = $this->getView()->url('kofus_user', array('controller' => 'autologout'));
        $urlLogout = $this->getView()->url('kofus_user', array('controller' => 'autologout', 'action' => 'logout'));
        
        $this->view->headScript()->appendScript("

	       setInterval(function(){
        		$.ajax({
        			type: 'POST',
        			url: '".$urlAutologout."',
        			dataType: 'json',
        			success: function(data) {
        				if (data['trigger_logout']) {
        					self.location.href = '".$urlLogout."';
        				} else {
        					$('.autologout-diff-h').html(data['diff_h']);
        				}
        			}
        		});
    	   }, 25000);
        ");
        
    }
    
    
    
}