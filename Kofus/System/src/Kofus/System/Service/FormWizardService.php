<?php

namespace Kofus\System\Service;

use Kofus\System\Service\AbstractService;
use Kofus\System\FormWizard\Page;

class FormWizardService extends AbstractService
{
    protected $pages = array();
    
    
    public function addPage(Page $page)
    {
        $session = $this->getSession();
        
        // Build form
        $fb = $this->getServiceLocator()->get('KofusFormBuilderService');
        $fb->reset();
        if ($page->getFormConfig())
            $fb->setConfig($page->getFormConfig());
        $fb->setObject($this->object);
        $form = $fb->buildForm();
        $page->setForm($form);
        
        // Populate form values from session
        if (isset($session->pages[$page->getId()])) {
            $page->setValues($session->pages[$page->getId()]);
        } 
        
        $this->pages[$page->getId()] = $page;
        
    }
    
    public function getPages()
    {
        return $this->pages;
    }
    
    public function getPage($pageId)
    {
        return $this->pages[$pageId];
    }
    
    public function getActivePage()
    {
        foreach ($this->pages as $page) {
            
            // requested?
            if ($this->getRequestedPageId() == $page->getId())
                break;
            
            // form values incomplete?
            $form = clone $page->getForm();
            $form->setData($page->getValues());
            if (! $form->isValid())
                break;
        }
        return $page;
    }
    
    public function isCompleted()
    {
        foreach ($this->pages as $page) {
            
            // form values incomplete?
            $form = clone $page->getForm();
            $form->setData($page->getValues());
            if (! $form->isValid())
                return false;
        }
        return true;
        
    }
    
    public function reset()
    {
        $session = $this->getSession();
        $session->pages = array();
    }
    
    public function getPreviousPage(Page $page)
    {
        $pages = array_reverse($this->pages);
        $_page = array_pop($pages);
        while ($_page) {
            if ($_page->getId() == $page->getId()) {
                return array_pop($pages);
            }
            $_page = array_pop($pages);
        }
    }
    
    public function getNextPage(Page $page)
    {
        $pages = $this->pages;
        $_page = array_shift($pages);
        while ($_page) {
            if ($_page->getId() == $page->getId()) {
                return array_shift($pages);
            }
            $_page = array_shift($pages);
        }
    }
    
    /**
     * Is page ready for user interaction?
     * @param Page $page
     */
    public function isPageReady(Page $page)
    {
        $activePage = $this->getActivePage();
        foreach ($this->pages as $_page) {
            if ($activePage->getId() == $_page->getId())
                return false;
            if ($page->getId() == $_page->getId())
                return true;
        }
        return false;
    }
    
    protected $requestedPageId;
    
    public function setRequestedPageId($value)
    {
        $this->requestedPageId = $value; return $this;
    }
    
    public function getRequestedPageId()
    {
        return $this->requestedPageId;
    }
    
    protected $object;
    
    public function setObject($object)
    {
        $this->object = $object; return $this;
    }
    
    public function getObject($aggregate=false)
    {
        if (! $aggregate)
            return $this->object;
        foreach ($this->getPages() as $page) {
            $form = $page->getForm();
            $form->setData($page->getValues());
            $form->isValid();
        }
        
        return $form->getData();
    }
    
    protected $session;
    
    public function getSession()
    {
        if (! $this->session)
            $this->session = new \Zend\Session\Container('FormWizardService');
        return $this->session;
    }
	
    public function savePage(Page $page)
    {
        $session = $this->getSession();
        if (! isset($session->pages)) {
            $pages = array();
        } else {
            $pages = $session->pages; 
        }
        
        $pages[$page->getId()] = $page->getValues();
        
        $session->pages = $pages;
        
        return $this;
    }
	
}