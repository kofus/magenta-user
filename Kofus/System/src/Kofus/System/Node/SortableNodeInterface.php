<?php

namespace Kofus\System\Node;

interface SortableNodeInterface
{
    public function getPriority();
    
    public function setPriority($value);
}