<?php

namespace Kofus\System\Node;

interface ParameterInterface
{
    public function setParam($key, $value);
    
    public function getParam($key);
}