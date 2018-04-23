<?php 

namespace Kofus\System\FormWizard;

class Page
{
    protected $params = array();
    
    public function setParam($key, $value)
    {
        $this->params[$key] = $value; return $this;
    }
    
    public function getParam($key)
    {
        return $this->params[$key];
    }
    
    public function setTitle($value)
    {
        $this->setParam('title', $value);
    }
    
    public function getTitle()
    {
        return $this->getParam('title');
    }
    
    protected $values = array();
    
    public function setValues(array $values)
    {
        $this->values = $values; return $this;
    }
    
    public function getValues()
    {
        return $this->values;
    }
    
    protected $form;
    
    public function setForm(\Zend\Form\Form $form)
    {
        $this->form = $form;
    }
    
    public function getForm()
    {
        return $this->form;
    }
    
}