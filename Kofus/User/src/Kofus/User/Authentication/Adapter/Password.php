<?php
namespace Kofus\User\Authentication\Adapter;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result;


class Password extends AbstractAdapter implements AdapterInterface
{
    
    public function authenticate()
    {
        $bcrypt = new \Zend\Crypt\Password\Bcrypt();
        if ($bcrypt->verify($this->getCredential(), $this->getIdentity()->getCredential())) 
        	return new Result(Result::SUCCESS, $this->getIdentity());
        return new Result(Result::FAILURE_CREDENTIAL_INVALID, $this->getIdentity());
        
    }
    
    
    
}

