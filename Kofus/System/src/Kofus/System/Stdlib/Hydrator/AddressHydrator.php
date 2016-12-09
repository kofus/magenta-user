<?php
namespace Kofus\System\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;

class AddressHydrator implements HydratorInterface
{
    public function extract($object)
    {
        $data = array();
        $data['recipient'] = $object->getRecipient();
        $data['additional'] = $object->getAdditional1();
        $data['street'] = $object->getStreet();
        $data['postal_code'] = $object->getPostCode();
        $data['city'] = $object->getCity();
        $data['country'] = $object->getCountry();        
        return $data;
    }
    
    public function hydrate(array $data, $object)
    {
        $object->setRecipient($data['recipient']);
        $object->setAdditional1($data['additional']);
        $object->setStreet($data['street']);
        $object->setPostCode($data['postal_code']);
        $object->setCity($data['city']);
        $object->setCountry($data['country']);      
          
        return $object;
    }
    
}