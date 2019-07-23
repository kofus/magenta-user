<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;



class AutologoutController extends AbstractActionController
{
    public function indexAction()
    {
        // Session with heartbeat
        $session = new \Zend\Session\Container('autologout');
        if (! isset($session->heartbeat))
            $session->heartbeat = time();
        $heartbeatDiff = time() - $session->heartbeat;
        
        // PHP / user settings
        $cookieLifetime = (int) ini_get('session.cookie_lifetime');
        $maxLifetime = (int) ini_get('session.gc_maxlifetime');
        $requestedLifetime = $this->config()->get('user.autologout', 24*60);
        
        // Cacl. min lifetime
        $min = null;
        if ($cookieLifetime && ($min === null || $cookieLifetime < $min))
            $min = $cookieLifetime;
        if ($maxLifetime && ($min === null || $maxLifetime < $min))
            $min = $maxLifetime;
        if ($min === null || $requestedLifetime < $min)
            $min = $requestedLifetime;
        
        // User
        $userId = null;
        if ($this->user()->getAccount())
            $userId = $this->user()->getAccount()->getNodeId();
        
        // Trigger logout?
        $diff = max($min - $heartbeatDiff, 0);
        $triggerLogout = $userId && $diff < 60;
        
        $data = array();
        $data['cookie_lifetime'] = $cookieLifetime;
        $data['gc_maxlifetime'] = $maxLifetime;
        $data['requested_lifetime'] = $requestedLifetime;
        $data['requested_lifetime_h'] = $this->renderPeriod($requestedLifetime);
        $data['min_lifetime'] = $min;
        $data['min_lifetime_h'] = $this->renderPeriod($min);
        $data['heartbeat'] = $session->heartbeat;
        $data['heartbeat_diff'] = $heartbeatDiff;
        $data['heartbeat_diff_h'] = $this->renderPeriod($heartbeatDiff);
        $data['diff'] = $diff;
        $data['diff_h'] = $this->renderPeriod($diff);
        $data['trigger_logout'] = $triggerLogout;
        
        return new JsonModel($data);
        
        
    }
    
    public function logoutAction()
    {
        $account = $this->user()->getAccount();
        if ($account) {
            $this->user()->logout();
            $this->flashMessenger()->addSuccessMessage($this->translator()->translate('Goodbye') . ', ' . $account->getName('display') . '.');
        }
        
        return $this->redirect()->toUrl($this->archive()->uriStack()->pop());
            
    }
    
    public function heartbeatAction()
    {
        $session = new \Zend\Session\Container('autologout');
        $session->heartbeat = time();
        return new JsonModel(array(
            'heartbeat' => $session->heartbeat
        ));
    }
    
    protected function renderPeriod($sec)
    {
        $value = $sec / 60;
        
        $hour = floor($value / 60);
        $minutes = $value % 60;
        
        if (! $hour) {
            return $minutes . ' min.';
        } else {
            return $hour . ' St. ' . $minutes . ' min.';
        }
        
    }
    
    
    
}