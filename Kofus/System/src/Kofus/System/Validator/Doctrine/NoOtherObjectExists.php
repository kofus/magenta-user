<?php

namespace Kofus\System\Validator\Doctrine;


use DoctrineModule\Validator\NoObjectExists;

class NoOtherObjectExists extends NoObjectExists
{
    private $id_field; //id of the entity to edit
    private $id_getter;  //getter of the id
    private $additionalFields = null; //other fields
    public function __construct(array $options)
    {
        parent::__construct($options);
        if (isset($options['additionalFields'])) {
            $this->additionalFields = $options['additionalFields'];
        }
        $this->id_field = $options['id_field'];
        $this->id_getter = $options['id_getter'];
    }
    public function isValid($value, $context = null)
    {
        if (null != $this->additionalFields && is_array($context)) {
            $value = (array) $value;
            foreach ($this->additionalFields as $field) {
                $value[] = $context[$field];
            }
        }
        $value = $this->cleanSearchValue($value);
        $match = $this->objectRepository->findOneBy($value);
        
        if (is_object($match) && $match->{$this->id_getter}() != $context[$this->id_field]) {
            if (is_array($value)) {
                $str = '';
                foreach ($value as $campo) {
                    if ($str != '') {
                        $str .= ', ';
                    }
                    $str .= $campo;
                }
                $value = $str;
            }
            $this->error(self::ERROR_OBJECT_FOUND, $value);
            return false;
        }
        return true;
    }
    
}