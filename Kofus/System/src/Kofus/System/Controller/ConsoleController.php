<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Form\Element\Submit;
use Zend\Form\Form;


class ConsoleController extends AbstractActionController
{
    public function rebuildLuceneIndexAction()
    {
        print $this->lucene()->reindex($this->params('node_type'));
    }
    
    public function optimizeAction()
    {
        // File nodes
        print 'Delete file nodes with missing file...' . PHP_EOL;
        foreach ($this->config()->get('nodes.enabled') as $nodeType) {
            $entityClass = $this->config()->get('nodes.available.' . $nodeType . '.entity');
            if (! $entityClass) continue;
            $entity = new $entityClass();
            if (is_a($entity, 'Kofus\Media\Entity\FileEntity')) {
                $files = $this->nodes()->getRepository($nodeType)->findAll();
                foreach ($files as $file) {
                    $path = $file->getPath();
                    if (! file_exists($path)) {
                        print 'DELETE NODE ' . $file . ' > ' . $file->getPath() . PHP_EOL;
                        $this->em()->remove($file);
                    }
                }
            }
        }
        $this->em()->flush();
        
        // Links
        print 'Delete node links with missing node...' . PHP_EOL;
        $links = $this->em()->getRepository('Kofus\System\Entity\LinkEntity')->findAll();
        foreach ($links as $link) {
            $linkedNodeId = $link->getLinkedNodeId();
            $linkedNode = $this->nodes()->getNode($linkedNodeId);
            if (! $linkedNode) {
                print 'DELETE LINK ' . $linkedNode . PHP_EOL;
                $this->em()->remove($link);
            }
        }
        $this->em()->flush();
        
        // Relations
        print 'Delete node relations with missing node...' . PHP_EOL;
        $relations = $this->em()->getRepository('Kofus\System\Entity\RelationEntity')->findAll();
        foreach ($relations as $relation) {
            $node1 = $this->nodes()->getNode($relation->getNode1Id());
            $node2 = $this->nodes()->getNode($relation->getNode1Id());
            if (! $node1 || ! $node2) {
                print 'DELETE RELATION ' . $relation . PHP_EOL;
                $this->em()->remove($relation);
            }
        }
        $this->em()->flush();
    }
    
}
