<?php

namespace Kofus\System\Collection;



abstract class AbstractCollection
{
	protected $lineItems = array();
	
	protected $hydratedLineItems = array();
	
	protected function getHydratedLineItems()
	{
	    if (! $this->hydratedLineItems) {
	        $this->hydratedLineItems = array();
	        foreach ($this->lineItems as $type => $delta) {
	            foreach ($delta as $id => $data) {
	                $lineItem = $data['class']::hydrate($data['data']);
	                $this->hydratedLineItems[$type][$id] = $lineItem;
	            }
	        }
	    }
	    return $this->hydratedLineItems;
	}
	
	public function addLineItem($lineItem)
	{
	    if ($lineItem->getQuantity() !== null) {
		  $this->lineItems[$lineItem->getType()][$lineItem->getId()] = array('class' => get_class($lineItem), 'data' => $lineItem->extract());
	    } else {
	        unset($this->lineItems[$lineItem->getType()][$lineItem->getId()]);
	    }
		$this->hydratedLineItems = null;
		return $this;
	}
	
	public function removeLineItems($type)
	{
		unset($this->lineItems[$type]);
		$this->hydratedLineItems = null;
		return $this;
	}
	
	public function getLineItems($types=array())
	{
	    if (is_string($types)) $types = array($types);
	    $lineItems = array();
	    foreach ($this->getHydratedLineItems() as $type => $delta) {
	        foreach ($delta as $id => $lineItem) {
	            if ($types == array() || in_array($lineItem->getType(), $types))
	                $lineItems[] = $lineItem;
	        }
	    }
	    return $lineItems;
	}
	
	public function getLineItem($types=array(), $id=null)
	{
	    if (is_string($types)) $types = array($types);
	    foreach ($this->getHydratedLineItems() as $type => $delta) {
	    	foreach ($delta as $key => $lineItem) {
	    	    if ($id) {
	    	        if ($key == $id) return $lineItem;
	    	    } else {
                    if ($types == array() || in_array($lineItem->getType(), $types))
	    		 	   return $lineItem;
	    	    }
	    	}
	    }
	}
	
	public function getQuantity($types=array(), $id=null)
	{
	    if (is_string($types)) $types = array($types);
	    $quantity = 0;
	    foreach ($this->getHydratedLineItems() as $type => $delta) {
	    	foreach ($delta as $lineItemId => $lineItem) {
	    		if ($types == array() || in_array($lineItem->getType(), $types) && $id === null || in_array($lineItem->getType(), $types) && $id == $lineItemId)
	    			$quantity += $lineItem->getQuantity();
	    	}
	    }
	    return $quantity;
	}
	
	public function count($types=array(), $id=null)
	{
	    if (is_string($types)) $types = array($types);
	    $count = 0;
	    foreach ($this->getHydratedLineItems() as $type => $delta) {
	    	foreach ($delta as $lineItemId => $lineItem) {
	    		if ($types == array() || in_array($lineItem->getType(), $types) && $id === null || in_array($lineItem->getType(), $types) && $id == $lineItemId)
	    			$count += $lineItem->getQuantity();
	    	}
	    }
	    return $count;
	}
	
	public function getLineItemTypes()
	{
	    $types = array();
	    foreach ($this->getHydratedLineItems() as $type => $delta)
	        $types[] = $type;
	    return $types;
	}
	
}

