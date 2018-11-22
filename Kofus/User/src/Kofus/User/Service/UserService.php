<?php

namespace Kofus\User\Service;

use Zend\Authentication\Result;
use Zend\Authentication\Storage\Session as Storage;
use Kofus\User\Authentication\Adapter as AuthAdapter;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Kofus\System\Service\AbstractService;

class UserService extends AbstractService implements EventManagerAwareInterface
{
    public function encrypt($password, $encryption='password')
    {
        switch ($encryption) {
            case 'md5':
                $crypt = md5($password);
                break;
            case 'password':
                $bcrypt = new \Zend\Crypt\Password\Bcrypt();
                $crypt = $bcrypt->create($password);
                break;
            case 'plaintext':
                $crypt = $password;
                break;
            throw new \Exception('Unknown encryption method: ' . $encryption);
            
        }
        return $crypt;
    }
    
	public function login($identity, $credential=null, $type='login')
	{
	    // Get auth entity by identity
        $auth = $this->nodes()->getRepository('AUTH' . strtoupper($type))->findOneBy(array(
            'identity' => $identity,
            'enabled' => true
        ));

        if (! $auth)
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $identity);
        $account = $auth->getAccount();
        if (! $account || $account->getStatus() != 1)
            return new Result(Result::FAILURE_UNCATEGORIZED, $identity);
        
        if ('login' == $type) {
            switch ($auth->getEncryption()) {
            	case 'md5':
            	    $authAdapter = new AuthAdapter\Md5();
            	    break;
            	    
            	case 'drupal':
            	    $authAdapter = new AuthAdapter\Drupal();
            	    break;
            	    
            	case 'password':
            	    $authAdapter = new AuthAdapter\Password();
            	    break;
            	    
            	default:
            	    throw new \Exception('Encryption type not supported: ' . $auth->getEncryption());
            }
        } else {
            $authAdapter = new AuthAdapter\Passphrase();
        }
        
        $authAdapter->setIdentity($auth)->setCredential($credential);
        $result = $authAdapter->authenticate();
        if ($result->isValid()) {
            $storage = new Storage();
            $storage->write($auth->getNodeId());
            $this->getEventManager()->trigger('login', $this, array('account' => $this->getAccount()));
        }
        
        $account->setTimestampLogin(new \DateTime());
        $this->em()->persist($account);
        $this->em()->flush();
        
        return $result;
	}
	
	public function logout()
	{
	    $account = $this->getAccount();
	    $storage = new Storage();
	    $storage->clear();
	    $this->getEventManager()->trigger('logout', $this, array('account' => $account));
	}
	
	public function getAuth()
	{
	    $storage = new Storage();
	    $authNodeId = $storage->read();
	    if ($authNodeId) {
	       $auth = $this->nodes()->getNode($authNodeId);
	       if (! $auth instanceof \Kofus\User\Entity\AuthEntity)
	           throw new \Exception('Not an authentication node: ' . $authNodeId);
	       return $auth;
	    }
	    
	}
	
	public function getAccount()
	{
	    if ($this->getAuth())
	       return $this->getAuth()->getAccount();
	}
	
	public function getRole()
	{
	    if ($this->getAccount())
	        return $this->getAccount()->getRole();
	    return 'Guest';
	}
	
	protected $acl;
	
	public function acl()
	{
        if (! $this->acl)
            $this->acl = $this->getServiceLocator()->get('KofusAclService')->getAcl();
        return $this->acl;
	}
	
	public function isAllowed($resource, $action=null)
	{
		return $this->acl()->isAllowed($this->getRole(), $resource, $action);
	}
	
	public function deleteAccount(\Kofus\User\Entity\AccountEntity $entity)	
	{
	    $nodes = $this->getServiceLocator()->get('KofusNodeService');
	    
	    // Delete AUTHs
	    $auths = $nodes->getRepository('AUTH')->findBy(array('account' => $entity));
	    foreach ($auths as $auth)
	        $this->em()->remove($auth);
	    
	    // Delete relations
	    $nodes->deleteRelations($entity);
	    
	    // Delete cms links
	    $links = $this->getServiceLocator()->get('KofusLinkService');
	    $links->deleteNodeLinks($entity);
	    
	    // Delete node itself
	    $nodes->deleteNode($entity);
	     
	}
	
	protected $events;
	
	public function setEventManager(EventManagerInterface $events)
	{
	    $events->setIdentifiers(array('KOFUS_USER', get_called_class()));
	    $this->events = $events;
	    return $this;
	}
	
	public function getEventManager()
	{
	    if (null === $this->events)
	        $this->setEventManager(new EventManager());
	    
	    return $this->events;
	}
}