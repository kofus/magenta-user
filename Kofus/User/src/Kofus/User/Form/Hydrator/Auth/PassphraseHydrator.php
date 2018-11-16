<?php
namespace Kofus\User\Form\Hydrator\Auth;

use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PassphraseHydrator implements HydratorInterface, ServiceLocatorAwareInterface
{

    public function extract($object)
    {
        $accountId = null;
        if ($object->getAccount())
            $accountId = $object->getAccount()->getNodeId();
        if (! $accountId && isset($_GET['account']))
            $accountId = $_GET['account'];
        
        return array(
            'identity' => $object->getIdentity(),
            'account' => $accountId,
            'enabled' => $object->isEnabled()
        );
    }

    public function hydrate(array $data, $object)
    {
        // Fetch user account
        $account = $this->getServiceLocator()->get('KofusNodeService')->getNode($data['account'], 'U');      
        if (! $account)
            throw new \Exception('A user account is required');

        // Setters
        $object->setIdentity($data['identity']);
        $object->setAccount($account);
        $object->isEnabled($data['enabled']);
        
        return $object;
    }
    
    protected $sm;
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
    	$this->sm = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
    	return $this->sm;
    }    
}