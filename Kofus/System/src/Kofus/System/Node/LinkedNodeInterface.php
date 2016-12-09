<?php

namespace Kofus\System\Node;

interface LinkedNodeInterface
{
    public function getUriSegment();
    
    public function getParent();
}