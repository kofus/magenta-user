<?php
namespace Kofus\User\Authentication\Adapter;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result;
use Kofus\User\Entity\AuthPassphraseEntity;


class Passphrase extends AbstractAdapter implements AdapterInterface
{
    
    public function authenticate()
    {
        if ($this->getIdentity() instanceof AuthPassphraseEntity)
            return new Result(Result::SUCCESS, $this->getIdentity());
        return new Result(Result::FAILURE_CREDENTIAL_INVALID, $this->getIdentity());
    }
    
    
    
}

