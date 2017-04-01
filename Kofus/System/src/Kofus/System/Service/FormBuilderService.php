<?php
namespace Kofus\System\Service;

use Zend\Form\Form;
use Kofus\System\Stdlib\Hydrator\DummyHydrator;
use Zend\Stdlib\InitializableInterface;
use Zend\Form\FieldsetInterface;
use Zend\InputFilter\InputFilterProviderInterface;

/*
 * --------------- Example Config ------------- 'fieldsets' => array( 'master' => array( 'class' => 'Database\Form\Fieldset\Annotation\MasterFieldset', 'hydrator' => 'Database\Form\Hydrator\Annotation\MasterHydrator' ) )
 */
use Kofus\System\Service\AbstractService;

class FormBuilderService extends AbstractService
{

    protected $config = array();

    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    public function getConfig($key=null, $default=null)
    {
        if ($key) {
            if (isset($this->config[$key]))
                return $this->config[$key];
            return $default;
        }
        return $this->config;
    }

    protected $object;

    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }

    protected $sections = array();

    /*
    protected $elementOptions = array(
        'column-size' => 'sm-12',
        'label_attributes' => array(
            'class' => 'col-sm-12'
        )  
    ); */

    public function addSection($fieldset, $hydrator = null, $name = null)
    {
        // Create fieldset object
        if (is_string($fieldset)) {
            if (! $this->getServiceLocator()->has($fieldset))
                $this->getServiceLocator()->setInvokableClass($fieldset, $fieldset);
            $fieldset = $this->getServiceLocator()->get($fieldset);
        }
        
        // Create hydrator object
        if (is_string($hydrator)) {
            if (! $this->getServiceLocator()->has($hydrator))
                $this->getServiceLocator()->setInvokableClass($hydrator, $hydrator);
            $hydrator = $this->getServiceLocator()->get($hydrator);
        }
        
        // Initialize fieldset
        if ($name)
            $fieldset->setName($name);
        
        $this->sections[$name] = array(
            'fieldset' => $fieldset,
            'hydrator' => $hydrator
        );
        
        return $this;
    }

    protected function buildSections()
    {
        foreach ($this->sections as $section) {
            // Object
            if ($this->getObject())
                $section['fieldset']->setObject($this->getObject());
                
                // init
            if ($section['fieldset'] instanceof InitializableInterface)
                $section['fieldset']->init();
                
                // hydrator
            if ($this->getObject()) {
                $section['fieldset']->setHydrator($section['hydrator']);
                $values = $section['fieldset']->getHydrator()->extract($section['fieldset']->getObject());
                $section['fieldset']->populateValues($values);
            }
            
            $this->form->add($section['fieldset']);
        }
    }

    protected $form;

    protected function decorateElementOptions($form)
    {
        foreach ($form as $element) {
            if ($element instanceof FieldsetInterface)
                return $this->decorateElementOptions($element);
            
            // element options
            foreach ($this->getConfig('element_options', array()) as $key => $value)
                $element->setOption($key, $value);
            
            // Add default value to submit buttons
            if ($element instanceof \Zend\Form\Element\Submit) {
                if (! $element->getValue())
                    $element->setValue($element->getName());
            }
            
        }
    }

    protected function decorateRequiredFields($fieldset, array $spec)
    {
        foreach ($fieldset as $element) {
            if (isset($spec[$element->getName()]['required']) && $spec[$element->getName()]['required']) {
                $labelAttributes = array();
                if ($element->getOption('label_attributes'))
                    $labelAttributes = $element->getOption('label_attributes');
                $css = array();
                if (isset($labelAttributes['class']))
                    $css = explode(' ', $labelAttributes['class']);
                $css[] = 'required';
                $labelAttributes['class'] = implode(' ', $css);
                $element->setOption('label_attributes', $labelAttributes);
            }
        }
    }

    public function buildForm($name = null, $options = array())
    {
        $this->form = new Form($name, $options);
        $this->form->setHydrator(new DummyHydrator());
        
        // Deploy from config
        if (isset($this->config['sections'])) {
            foreach ($this->config['sections'] as $name => $data) {
                $fieldset = (isset($data['fieldset'])) ? $data['fieldset'] : null;
                $hydrator = (isset($data['hydrator'])) ? $data['hydrator'] : null;
                $this->addSection($fieldset, $hydrator, $name);
            }
        }
        
        $this->buildSections();
        // $this->buildTranslationFieldsets();
        // $this->buildLayout($this->form);
        
        // Form id?
        if ($name) {
            $el = new \Kofus\System\Form\Element\Immutable\Hidden('form_id');
            $el->setImmutableValue($name);
            $this->form->add($el);
        }
        
        $el = new \Zend\Form\Element\Csrf('csrf');
        $this->form->add($el);
        
        // Element options
        $this->decorateElementOptions($this->form);
        
        // Required fields
        foreach ($this->form->getFieldsets() as $fieldset) {
            if ($fieldset instanceof InputFilterProviderInterface) {
                $spec = $fieldset->getInputFilterSpecification();
                $this->decorateRequiredFields($fieldset, $spec);
            }
        }
        
        if ($this->getObject())
            $this->form->bind($this->getObject());
            
            /*
         * if ($this->getObject()) $this->form->bind($this->getObject()); if ($this->getEntity()) $this->form->bind($this->getEntity());
         */
        
        $this->form->prepare();
        
        return $this->form;
    }
}