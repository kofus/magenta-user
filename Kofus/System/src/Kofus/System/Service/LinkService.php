<?php
namespace Kofus\System\Service;

use Kofus\System\Node\NodeInterface;
use Kofus\System\Node\TranslatableNodeInterface;
use Kofus\System\Node\LinkedNodeInterface;
use Kofus\System\Service\AbstractService;

class LinkService extends AbstractService
{
    public function add(NodeInterface $node, $uri, array $options=array())
    {
        $link = new \Kofus\System\Entity\LinkEntity();
        $link->setUri($uri)
            ->setLinkedNodeId($node->getNodeId());
        
        if (isset($options['locale']))
            $link->setLocale($options['locale']);
        if (isset($options['context']))
        	$link->setContext($options['context']);
        
        
        $this->em()->persist($link);
        $this->em()->flush();
        
        return $this;
    }
    
    public function deleteNodeLinks(NodeInterface $node, array $options=array())
    {
        $qb = $this->em()->createQueryBuilder()
            ->delete('Kofus\System\Entity\LinkEntity', 'l')
            ->where('l.linkedNodeId = :node_id')
            ->setParameter('node_id', $node->getNodeId());
        
        if (isset($options['locale']))
            $qb->andWhere('l.locale = :locale')
                ->setParameter('locale', $options['locale']);
        
        return $qb->getQuery()->execute();
    }
    
    public function getTranslationLink(NodeInterface $node, $locale)
    {
        $link = $this->em()->createQueryBuilder()
            ->select('l')
            ->from('Kofus\System\Entity\LinkEntity', 'l')
            ->where('l.linkedNodeId = :node_id')
            ->setParameter('node_id', $node->getNodeId())
        	->andWhere('l.locale = :locale')
        	->setParameter('locale', $locale)
        	->getQuery()->getOneOrNullResult();
        
        return $link;
    }
    
    public function createNodeLink(NodeInterface $node, $uri, $locale=null)
    {
        $this->deleteNodeLinks($node, array('locale' => $locale));
        $this->add($node, $uri, array('locale' => $locale));
    }
    
    protected function buildUriSegments(LinkedNodeInterface $node, $locale)
    {
        $segments = array();
        
        $t = $this->getServiceLocator()->get('KofusTranslationService');
        $t->getTranslator()->setLocale($locale);
        
        $_node = $node;
        while ($_node) {
        	$segments[] = $t->translateNode($_node, 'getUriSegment');
        	$_node = $_node->getParent();
        }
        $segments = array_reverse($segments);
        $uriSegments = implode('/', $segments);
        return $uriSegments;
    }
    
    
    
    public function rebuildLinks(NodeInterface $node)
    {
        if (! $node instanceof LinkedNodeInterface)
            return;
        
        $serviceNodes = $this->getServiceLocator()->get('KofusNodeService');
        $patterns = $this->config()->get('nodes.available.'.$node->getNodeType().'.links', array());
        if ($node instanceof TranslatableNodeInterface) {
            $locales = $this->config()->get('locales.available');
        } else {
            $locales = array('de_DE');
        }
                
        foreach ($patterns as $context => $pattern) {
            foreach ($locales as $locale) {
                $language = substr($locale, 0, 2);
                
                // Substitute language
                $link = str_replace('{:language}', $language, $pattern);
                
                // Build uriSegment
                $link = str_replace('{:uriSegments}', $this->buildUriSegments($node, $locale), $link);
                
                // Related nodes?
                if (preg_match_all('/\{\:NODE_([A-Z]+)\}/', $link, $matches)) {
                    foreach ($matches[1] as $nodeType) {
                        $relNode = $serviceNodes->getRelatedNode($node, $nodeType);
                        if ($relNode)
                            $link = str_replace('{:NODE_'.$nodeType.'}', $this->buildUriSegments($relNode), $link);
                    }
                }
                
                $this->deleteNodeLinks($node, array('locale' => $locale));
                $linkEntity = new \Kofus\System\Entity\LinkEntity();
                $linkEntity->setUri($link)
                    ->setLinkedNodeId($node->getNodeId())
                	->setLocale($locale)
                	->setContext($context);
                
                $this->em()->persist($linkEntity);
                
            }
        }
        $this->em()->flush();
    }
    
    
    
}