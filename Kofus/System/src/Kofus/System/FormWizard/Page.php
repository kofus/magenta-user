<?php 

namespace Kofus\System\FormWizard;

class Page
{
    public function __construct(array $options=array())
    {
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method))
                $this->$method($value);
        }
    }
    
    
    protected $id;
    
    public function setId($value)
    {
        $this->id = $value; return $this;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * @var array
     */
    protected $params = array();
    
    public function setParam($key, $value)
    {
        $this->params[$key] = $value; return $this;
    }
    
    public function getParam($key)
    {
        return $this->params[$key];
    }
    
    /**
     * Form values
     * @var array
     */
    protected $values = array();
    
    public function setValues(array $values)
    {
        $this->values = $values; return $this;
    }
    
    public function getValues()
    {
        return $this->values;
    }
    
    public function setTitle($value)
    {
        $this->setParam('title', $value);
    }
    
    public function getTitle()
    {
        return $this->getParam('title');
    }
    
    protected $formConfig = array();
    
    public function setFormConfig(array $config)
    {
        $this->formConfig = $config; return $this;
    }
    
    public function getFormConfig()
    {
        return $this->formConfig;
    }
    
    protected $form;
    
    public function setForm(\Zend\Form\Form $form)
    {
        $this->form = $form; return $this;
    }
    
    public function getForm()
    {
        return $this->form;
    }

    /*
    protected $formObject;
    
    public function setFormObject(&$object)
    {
        $this->formObject = $object; return $this;
    }
    
    public function getFormObject()
    {
        return $this->formObject;
    } */
    
    
    
    
    
}