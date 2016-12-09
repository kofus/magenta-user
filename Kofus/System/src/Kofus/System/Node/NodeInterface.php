<?php

namespace Kofus\System\Node;

interface NodeInterface
{
    public function getNodeId();
    public function getNodeType();
    public function __toString();
}