<?php
namespace Kofus\System\Service;

use Kofus\System\Node\NodeInterface;
use Kofus\System\Service\AbstractService;

class NodeService extends AbstractService
{
    public function getConfig($nodeType, $key=null)
    {
        $nodeType = \Zend\Filter\StaticFilter::execute($nodeType, 'Alpha');
        
        $query = 'nodes.available.'.$nodeType;
        if ($key)
            $query .= '.' . $key;
           
        $config = $this->config()->get($query);
        $enabled = $this->config()->get('nodes.enabled');
        if (! in_array($nodeType, $enabled))
            throw new \Exception('Node type "' . $nodeType . '" exists but has not been enabled');
        return $config;        
    }
    
    public function getRepository($nodeType)
    {
        $classname = $this->config()->get('nodes.available.' . $nodeType . '.entity');
        if (! $classname)
            throw new \Exception('No entity class defined for node type ' . $nodeType);
        
        return $this->em()->getRepository($classname);
    }
    
    public function getEntityClass($nodeType)
    {
        $classname = $this->config()->get('nodes.available.' . $nodeType . '.entity');
        if (! $classname)
        	throw new \Exception('No entity class defined for node type ' . $nodeType);
        $classname = '\\' . $classname;
        return $classname;
    }
    
    public function createNode($nodeType)
    {
        $classname = $this->getEntityClass($nodeType);
        return new $classname();        
    }
    
    public function createQueryBuilder($nodeType)
    {
        $qb = $this->em()->createQueryBuilder();
        $qb->select('n')
            ->from($this->getEntityClass($nodeType), 'n');
        return $qb;
    }
    
    protected $cachedNodes = array();
    
    public function getNode($nodeId, $assertTypes=null)
    {
        if (is_string($assertTypes))
            $assertTypes = array($assertTypes);
        if ($assertTypes) {
            $nodeType = \Zend\Filter\StaticFilter::execute($nodeId, 'Alpha');
            if (! in_array($nodeType, $assertTypes))
                throw new \Exception('Node type ' . $nodeType . ' not allowed here, only ' . implode('/', $assertTypes));
        }
        
        if (! isset($this->cachedNodes[$nodeId])) {
	        $classname = $this->getConfig($nodeId, 'entity');
	        if (! $classname)
	            throw new \Exception('No entity classname configuration found for node ' . $nodeId);
	        $id = \Zend\Filter\StaticFilter::execute($nodeId, 'Digits');
        	$node = $this->em()->getRepository($classname)->findOneBy(array('id' => $id));
        	$this->cachedNodes[$nodeId] = $node;
        }
        return $this->cachedNodes[$nodeId];
    }
    
    public function deleteNode($node)
    {
        $this->em()->remove($node);
        $this->em()->flush();
    }
    
    public function deleteRelation(\Kofus\System\Entity\RelationEntity $relation, $node=null)
    {
        $this->em()->remove($relation);
        $this->em()->flush();
        
        if ($node)
            $this->deleteNode($node);
    }
    
    public function deleteRelations($nodeId)
    {
        if ($nodeId instanceof NodeInterface)
            $nodeId = $nodeId->getNodeId();
        $this->em()->createQueryBuilder()
            ->delete('Kofus\System\Entity\RelationEntity', 'r')
            ->where('r.node1Id = :nodeId OR r.node2Id = :nodeId')
            ->setParameter('nodeId', $nodeId)
            ->getQuery()
            ->execute();
    }
    
    public function getRelation($relationId, $nodeTypeToDeploy=null)
    {
        $relation = $this->em()->getRepository('Kofus\System\Entity\RelationEntity')->findOneBy(array('id' => $relationId));
        if ($nodeTypeToDeploy) {
            $alpha = new \Zend\I18n\Filter\Alpha();
            if ($nodeTypeToDeploy == $alpha->filter($relation->getNode1Id())) {
                $relation->setNode($this->getNode($relation->getNode1Id()));
            } else {
                $relation->setNode($this->getNode($relation->getNode2Id()));
            }
        }
        return $relation;
    }
    
    public function getRelatedNode($nodeId, $nodeTypes=array(), $label=null)
    {
    	$relations = $this->getRelations($nodeId, $nodeTypes, $label);
    	$relatedNodes = array();
    	foreach ($relations as $relation)
    		return $relation->getNode();
    }
    
    
    public function getRelatedNodes($nodeId, $nodeTypes=array(), $label=null)
    {
        $relations = $this->getRelations($nodeId, $nodeTypes, $label);
        $relatedNodes = array();
        foreach ($relations as $relation) {
            $relNode = $relation->getNode();
            $relatedNodes[$relNode->getNodeId()] = $relNode;
        }
        return $relatedNodes;
    }
    
    public function getRelations($nodeId, $nodeTypes=array(), $label=null)
    {
        if (is_string($nodeTypes))
            $nodeTypes = array($nodeTypes);
        
        if ($nodeId instanceof NodeInterface)
            $nodeId = $nodeId->getNodeId();
        $qb = $this->em()->createQueryBuilder()
            ->select('r')
            ->from('Kofus\System\Entity\RelationEntity', 'r')
            ->where('r.node1Id = :nodeId OR r.node2Id = :nodeId')
            ->setParameter('nodeId', $nodeId);
        
        if ($label) {
            $qb->andWhere('r.label = :label')
                ->setParameter('label', $label);
        }
            
        $orClause = array();
        foreach ($nodeTypes as $_index => $nodeType) {
            $index = (int) $_index;
            $orClause[] = 'r.node1Id LIKE :node_type'.$index.' OR r.node2Id LIKE :node_type' . $index;
            $qb->setParameter('node_type'.$index, $nodeType . '%');            
        }
        $qb->andWhere(implode(' OR ', $orClause));
        
        $_relations = $qb->orderBy('r.weight')
            ->getQuery()->getResult();
        
        $relations = array();
        foreach ($_relations as $relation) {
            
            if ($relation->getNode1Id() == $nodeId) {
                $relNodeId = $relation->getNode2Id();
            } else {
                $relNodeId = $relation->getNode1Id();
            }
            $node = $this->getNode($relNodeId);
            if (! $node)
                throw new \Exception('Node ' . $relNodeId . ' in relation '.$relation->getId().' does not exist');
            $relation->setNode($node);
            $relations[] = $relation;
        }
        
        return $relations;
    }
    
    public function getNodes($nodeType)
    {
        $classname = $this->config()->get('nodes.available.'.$nodeType.'.entity');
        if (! $classname)
            throw new \Exception('No class defined for entity ' . $nodeType);
        return $this->em()->getRepository($classname)->findAll();
    }
    
    public function getLinkedNode($uri=null, array $nodeTypes=array(), $context=null, $locale=null)
    {
        $links = $this->getLinkedNodes($uri, $nodeTypes, $context, $locale);
        if ($links)
            return $links[0];         
    }
    
    public function getLinkedNodes($uri=null, array $nodeTypes=array(), $context=null, $locale=null)
    {
        if (! $uri)
            $uri = $_SERVER['REQUEST_URI'];
        
        $qb = $this->em()->createQueryBuilder()
            ->select('l.linkedNodeId')
            ->from('Kofus\System\Entity\LinkEntity', 'l')
            ->where('l.uri = :uri')
            ->setParameter('uri', $uri);
        
        if ($nodeTypes) {
            $clause = array();
            $counter = 0;
            foreach ($nodeTypes as $nodeType) {
                $clause[] = 'l.linkedNodeId LIKE :node_type_' . $counter;
                $qb->setParameter('node_type_' . $counter, $nodeType . '%');
                $counter += 1;
            }
            $qb->andWhere(implode(' OR ', $clause));
        }
        if ($context) {
            $qb->andWhere('l.context = :context')
                ->setParameter('context', $context);
        }
        if ($locale) {
            $qb->andWhere('l.locale = :locale')
                ->setParameter('locale', $locale);
        }
        $nodes = array();
        $links = $qb->getQuery()->getResult();
        foreach ($links as $link) 
            $nodes[] = $this->getNode($link['linkedNodeId']);
        return $nodes;
        
    }
    
    public function isNodeTypeEnabled($type)
    {
        return in_array($type, $this->config()->get('nodes.enabled'));
    }
    
    public function isRelationTypeEnabled($type)
    {
        return in_array($type, $this->config()->get('relations.enabled'));
    }
    
    public function getLink($nodeId, $context=null, $locale=null)
    {
        if ($nodeId instanceof NodeInterface)
        	$nodeId = $nodeId->getNodeId();
        
        $qb = $this->em()->createQueryBuilder()
            ->select('l')
            ->from('Kofus\System\Entity\LinkEntity', 'l')
            ->where('l.linkedNodeId = :node_id')
            ->setParameter('node_id', $nodeId);
        
        if ($context) {
            $qb->andWhere('l.context = :context')
                ->setParameter('context', $context);
        }
        
        if (! $locale) 
            $locale = $this->getServiceLocator()->get('KofusLocale')->getLocale();
        
        $qb->andWhere('l.locale = :locale OR l.locale IS NULL')
            ->setParameter('locale', $locale);
        
        
        $link = $qb->getQuery()->getOneOrNullResult();
        
        return $link;
        
    }
}