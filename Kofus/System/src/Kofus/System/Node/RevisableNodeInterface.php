<?php

namespace Kofus\System\Node;

interface RevisableNodeInterface
{
    public function getFieldValue($field);
    
    public function getFieldName($field);
    
}