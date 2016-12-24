<?php

namespace Kofus\System\Node;

interface NodeModifiedInterface
{
    public function getTimestampModified();
    
    public function setTimestampModified(\DateTime $datetime);

}