<?php
namespace Kofus\System\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Kofus\System\Service\AbstractService;

class FormService extends AbstractService
{
    public function reset()
    {
        $this->form = null;
        $this->fieldsets = array();
        $this->entity = null;
        $this->locales = array();
        $this->labelSize = null;
        $this->fieldSize = null;
        return $this;
    }
    
    protected $locales = array();
    
    protected $entity;
    
    public function setEntity(&$entity)
    {
        $this->entity = $entity; return $this;
    }
    
    public function getEntity()
    {
        return $this->entity;
    }
    
    protected $context = 'default';
    
    public function setContext($value)
    {
        $this->context = $value; return $this;
    }
    
    public function addTranslationFieldset($locale)
    {
        $this->locales[] = $locale; 
        return $this;
    }
    
    protected function buildTranslationFieldsets()
    {
        if (! $this->entity instanceof \Kofus\System\Node\TranslatableNodeInterface)
        	return;
        
        $methods = $this->entity->getTranslatableMethods();
        $tFieldsets = array();
        
        foreach ($this->locales as $locale) {
            foreach ($this->form as $fieldset) {
                if (! $fieldset instanceof \Zend\Form\FieldsetInterface)
                    continue;
                $translatableFields = array();
                foreach ($fieldset as $element) {
                	if (in_array($element->getName(), $methods))
                		$translatableFields[] = $element;
                }
                if ($translatableFields) {
                    $tFieldset = new \Zend\Form\Fieldset($locale . '_' . $fieldset->getName());
                    $tFieldset->setLabel($fieldset->getLabel());
                    foreach ($translatableFields as $_element) {
                    	$tElement = new \Zend\Form\Element\Text($_element->getName());
                    	$tElement->setLabel($_element->getLabel());
                    	$tElement->setAttributes($_element->getAttributes());
                    	$tFieldset->add($tElement);
                    }
                    $tFieldsets[] = $tFieldset;
                }
            }
        }
        foreach ($tFieldsets as $tFieldset)
            $this->form->add($tFieldset);
    }
    
    public function getContext()
    {
        return $this->context;
    }
    
    protected $fieldsets = array();
    
    public function addFieldset(\Zend\Form\FieldsetInterface $fieldset, \Zend\Stdlib\Hydrator\HydratorInterface $hydrator=null)
    {
        $this->fieldsets[] = array('fieldset' => $fieldset, 'hydrator' => $hydrator);
        return $this;
    }
    
    protected function buildFieldsets()
    {
        if ($this->getEntity()) {
        
        	// Add fieldsets
        	$service = $this->getServiceLocator()->get('KofusNodeService');
        	$fieldsets = $service->getConfig($this->getEntity()->getNodeType(), 'form.'.$this->getContext().'.fieldsets');
        	if (! $fieldsets && 'default' != $this->getContext())
        		$fieldsets = $service->getConfig($this->getEntity()->getNodeType(), 'form.default.fieldsets');
        
        	foreach ($fieldsets as $id => $fieldsetConfig) {
        		$obj = new $fieldsetConfig['class']();
        		if ($obj instanceof ServiceLocatorAwareInterface)
        			$obj->setServiceLocator($this->getServiceLocator());
        		$obj->setObject($this->getEntity());
        		$obj->init();
        		if (! $obj->getName()) $obj->setName($id);
        		if (! isset($fieldsetConfig['hydrator']))
        		    $fieldsetConfig['hydrator'] = '\Kofus\System\Stdlib\Hydrator\DummyHydrator';
                $hydrator = new $fieldsetConfig['hydrator']();
        		if ($hydrator instanceof ServiceLocatorAwareInterface)
        		    $hydrator->setServiceLocator($this->getServiceLocator());
        		$obj->setHydrator($hydrator);
        		$obj->populateValues($obj->getHydrator()->extract($obj->getObject()));
        		if (isset($fieldsetConfig['label']))
        		  $obj->setLabel($fieldsetConfig['label']);
        		$this->form->add($obj);
        
        	}
        } 
        
        foreach ($this->fieldsets as $pair) {
                $fieldset = $pair['fieldset'];
                if ($fieldset instanceof ServiceLocatorAwareInterface)
                	$fieldset->setServiceLocator($this->getServiceLocator());
                if ($this->getObject())
                    $fieldset->setObject($this->getObject());
                $fieldset->init();
                
                if (isset($pair['hydrator'])) {
                    $hydrator = $pair['hydrator'];
                    if (is_string($hydrator)) {
                        $hydrator = '\\' . $hydrator;
                        $hydrator = new $hydrator;
                    }
                    if ($hydrator instanceof ServiceLocatorAwareInterface)
                    	$hydrator->setServiceLocator($this->getServiceLocator());
                    $fieldset->setHydrator($hydrator);
                    if ($fieldset->getObject())
                        $fieldset->populateValues($fieldset->getHydrator()->extract($fieldset->getObject()));
                }
                $this->form->add($fieldset);
            
        }
    }
    
    protected $object;
    
    public function setObject($obj)
    {
        $this->object = $obj; 
        return $this;
    }
    
    public function getObject()
    {
        return $this->object;
    }
    
    
    protected $fieldSize;
    
    public function setFieldSize($value)
    {
        $this->fieldSize = $value; return $this;
    }
    
    public function getFieldSize()
    {
        return $this->fieldSize;
    }
    
    protected $labelSize;
    
    public function setLabelSize($value)
    {
        $this->labelSize = $value; return $this;
    }
    
    public function getLabelSize()
    {
        return $this->labelSize;
    }
    
    protected function buildLayout($form)
    {
        // Input filter spec
        $spec = array();
        if ($form instanceof \Zend\InputFilter\InputFilterProviderInterface)
        	$spec = $form->getInputFilterSpecification();
         
        foreach ($form as $element) {
        
        	// Dive into fieldsets
        	if ($element instanceof \Zend\Form\FieldsetInterface) {
        		$this->buildLayout($element);
        		continue;
        	}
        	
        	// Add type and name as css class
        	$css = 'form-element-name-' . $element->getName();
        	
        	if ($element->hasAttribute('type'))
        	    $css .= ' form-element-type-' . $element->getAttribute('type');
        	
        	$css = str_replace('_', '-', $css);
        	
            $element->setOption('twb-form-group-size', $css);

        	
        	
        	$fieldSize = $this->getFieldSize();
        	$labelSize = $this->getLabelSize();
        	
        	
        	if ($element instanceof \Zend\Form\Element\MultiCheckbox) {
        	    // necessary to skip the following "checkbox elseif"
        	} elseif ($element instanceof \Zend\Form\Element\Checkbox) {
        	    $labelSize = 'col-sm-12';
        	    $fieldSize = 'sm-12';
        	} elseif ($element instanceof \Zend\Form\Element\Submit) {
        	    $labelSize = 'col-sm-12';
        	    $fieldSize = 'sm-12';
        	}
        	    
        	
        	// Input field
        	if ($fieldSize)
        		$element->setOption('column-size', $fieldSize);
        
        	// Label
    		$attr = array();
    		$class = array();
    		if ($labelSize)
    			$class[] = $labelSize;
    		
    		
    		$name = $element->getName();
    		if ($spec && isset($spec[$name]['required']) && $spec[$name]['required'])
    			$class[] = 'required';
    			
    		if ($class) $attr['class'] = implode(' ', $class);
    		$element->setOption('label_attributes', $attr);
        }        
    }
    
    protected $form;
    
    public function buildForm($formId=null)
    {
        $this->form = new \Zend\Form\Form();
        $this->form->setHydrator(new \Kofus\System\Stdlib\Hydrator\DummyHydrator());
        $this->buildFieldsets();
        $this->buildTranslationFieldsets();
        $this->buildLayout($this->form);
        
        // Form id?
        
        if ($formId) {
        	$el = new \Kofus\System\Form\Element\Immutable\Hidden('form_id');
        	$el->setImmutableValue($formId);
        	$this->form->add($el);
        }
        
        $el = new \Zend\Form\Element\Csrf('csrf');
        $this->form->add($el);
        
        if ($this->getObject())
            $this->form->bind($this->getObject());
        if ($this->getEntity())
            $this->form->bind($this->getEntity());
        
        // Do not clone at this point!!
        
        return $this->form;
    }
    
}