<?php 
namespace Kofus\System\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kofus\System\Node\LinkedNodeInterface;


/**
 * @ORM\Entity
 */

class PageEntity extends ContentEntity implements LinkedNodeInterface
{
    
    public function getNodeType()
    {
        return 'PG';
    }
}