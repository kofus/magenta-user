<?php
namespace Kofus\User\Authentication\Adapter;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result;


class Md5 extends AbstractAdapter implements AdapterInterface
{
    
    public function authenticate()
    {
    	if (md5($this->getCredential()) == $this->getIdentity()->getCredential()) 
    		return new Result(Result::SUCCESS, $this->getIdentity());
    	return new Result(Result::FAILURE_CREDENTIAL_INVALID, $this->getIdentity());
    }
    
    
    
}

