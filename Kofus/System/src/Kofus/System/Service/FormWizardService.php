<?php

namespace Kofus\System\Service;

use Kofus\System\Service\AbstractService;
use Kofus\System\FormWizard\Page;

class FormWizardService extends AbstractService
{
    protected $pages = array();
    
    
    public function addPage(Page $page)
    {
        $this->pages[] = $page;
    }
    
    public function getPages()
    {
        return $this->pages;
    }
    
    public function getActivePage()
    {
        return $this->pages[0];
    }
	
	
}