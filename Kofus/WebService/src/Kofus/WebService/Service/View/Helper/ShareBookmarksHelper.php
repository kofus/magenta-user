<?php

namespace Kofus\Webservice\Service\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class ShareBookmarksHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $title;
    protected $url;
    
    public function getPageTitle()
    {
        if (! $this->title)
            $this->title = (string) $this->view->headTitle()->renderTitle();
        return $this->title;
    }
    
    public function setPageTitle($value)
    {
        $this->title = $value; return $this;
    }
    
    public function getPageUrl()
    {
        if (! $this->url)
            $this->url = $this->view->serverUrl(true);
        return $this->url;
    }
    
    public function setPageUrl($value)
    {
        $this->url = $value; return $this;
    }
    
	public function __invoke()
	{
	    return $this;
	}
	
	public function renderDropdown()
	{
	    $s = '<div class="dropup share share-dropdown">
            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded=true">
                <span class="fa fa-share"></span>
	        
                Artikel teilen
        	</button>
            <ul class="dropdown-menu">';
        foreach ($this->getNetworks() as $name => $network) {
            $s .= '<li>';
	    	$s .= '<a href="' . $network['url'] . '"  target="_blank" title="'.$this->view->escapeHtmlAttr('Bei ' . $name . ' teilen...').'">';
	    	$s .= '<i class="' . $network['icon'] . '"></i>&nbsp;&nbsp;';
	    	$s .= $name;
	    	$s .= '</a>';
            $s .= '</li>';
        }	       
	    
	    $s .= '</ul>';
	    $s .= '</div>';
	    return $s;
	}
	
	public function render()
	{
	    $this->getView()->headScript()->appendScript("
	           $(document).ready(function() {
                    $('.share a').click(function(e){
		            e.stopPropagation();
		           window.open($(this).attr('href'), 'share-dialog', 'width=626, height=436');
        		return false;
	           });
                });
	        ");
	    
	    $s = '<div class="share share-icons">';
		foreach ($this->getNetworks() as $name => $network) {
			$s .= '<a href="' . $network['url'] . '"  target="_blank" title="'.$this->view->escapeHtmlAttr($network['title']).'">';
			$s .= '<i class="' . $network['icon'] . ' fa-lg"></i>';
			$s .= '</a>&nbsp;&nbsp;';
		}
		$s .= '</div>';
		return $s;
	}
	
	
	protected function getNetworks()
	{
	    $networks = array();
	    $config = $this->getServiceLocator()->get('KofusConfig');
	    $available = $config->get('webservice.share-bookmarks.networks.available');
	    foreach ($config->get('webservice.share-bookmarks.networks.enabled', array()) as $networkId) {
	        if (! isset($available[$networkId]))
	            throw new \Exception('Sharing bookmarks: no specification found for network ' . $networkId);
	        $network = $available[$networkId];
	        $url = $network['url'];
	        $url = str_replace('{url}', $this->getPageUrl(), $url);
	        $url = str_replace('{title}', $this->getPageTitle(), $url);
	        $network['url'] = $url;
	        $networks[$networkId] = $network; 
	    }
	    return $networks;
	}
	
	public function __toString()
	{
	    return $this->render();
	}
	
	protected $sm;
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->sm = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->sm->getServiceLocator();
	}
	
} 