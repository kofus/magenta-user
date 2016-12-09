<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;
use DOMDocument;
use DOMElement;
use DOMText;

class SpamSpanHelper extends AbstractHelper
{
    
    protected $mainClass 		= 'U5ASGwlUlZnIE';
    protected $userClass 		= 'ya71XC6HwyNHk';
    protected $domainClass 	    = 'NGSViGUhvYnI';
    protected $atText			= ' (at) ';
    protected $url     	        = '/assets/spamspan/spamspan.js';
    protected static $singletonInclude = true;
    
    
    public function __invoke($html)
    {
        $html = $this->replaceMailLinks($html);
        return $html;
    }
    
    protected function replaceMailLinks($html)
    {
        // Skip empty inupt
        if (! trim($html)) return $html;
        
        // DOM aus HTML-Stream erzeugen
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        
        $substitutionCounter = 0;
        
        // Iteriere über alle Link-Knoten
        $elementList = $dom->getElementsByTagName('a');
        
        foreach ($elementList as $element) {
        
        	// Link muss Attribut "href" besitzen
        	if ($element->hasAttribute('href')) {
        		$href = $element->getAttribute('href');
        
        		// Attribut "href" muss den Eintrag "mailto:" enthalten
        		if (preg_match('/\s*mailto\s*\:\s*(([a-z0-9\-\_\.]+)\@([a-z0-9\-\_\.]+))/i', $href, $matches)) {
        
        			$parent = $element->parentNode;
        				
        			// Erzeuge neue Span-Knoten
        			$spamSpan = new DOMElement('span');
        			$userSpan = new DOMElement('span', $matches[2]);
        			$domainSpan = new DOMElement('span', $matches[3]);
        				
        			// Ersetze beestehenden Link-Knoten
        			$parent->replaceChild($spamSpan, $element);
        				
        			// Knoten einhängen
        			$spamSpan->setAttribute('class', $this->mainClass);
        			$spamSpan->appendChild($userSpan);
        			$spamSpan->appendChild(new DOMText($this->atText));
        			$spamSpan->appendChild($domainSpan);
        			$userSpan->setAttribute('class', $this->userClass);
        			$domainSpan->setAttribute('class', $this->domainClass);
        				
        			$substitutionCounter += 1;
        		}
        	}
        }
        
        // HTML-Stream aus DOM erzeugen
        if ($substitutionCounter > 0) {
        	$html = $dom->saveHTML();
        	$html = preg_replace('/^\<\!DOCTYPE .+\<body\>/is', '', $html);
        	$html = str_replace('</body></html>', '', $html);
        }
        
      	return $html;
        
    }
}