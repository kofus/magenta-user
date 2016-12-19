<?php
namespace Kofus\System\Listener;

use Zend\Mvc\MvcEvent;
use Zend\Mail;
use Zend\Mime;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;

class ErrorListener extends AbstractListenerAggregate implements ListenerAggregateInterface
{
    
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'handleDispatchError'));
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'handleDispatchError'));
    }
    
    
    /**
     * Send exception mail
     * @param MvcEvent $e
     */
    public function handleDispatchError(MvcEvent $e)
    {
    	if (! isset($_SERVER['DEVELOPER'])) {
    		$exception = $e->getParam('exception');
    		if (! $exception) return; 
    		$text = $this->exception2html($exception);
    		$text .= $this->getHtmlEnvParams();
    		
    		$html = new Mime\Part($text);
    		$html->type = 'text/html';
    		
    		$mimeBody = new Mime\Message();
    		$mimeBody->setParts(array($html));
    		
    		$mail = new Mail\Message();
    		$mail->setSubject('[' . $_SERVER['HTTP_HOST'] . '] EXCEPTION');
    		$mail->addTo('log@kofus.de');
    		$mail->setBody($mimeBody);
    		$mail->setFrom('log@kofus.de', 'Fehler-Manager');
    		
    		$transport = new Mail\Transport\Sendmail();
    		$transport->send($mail);
    	}
    }
    
    /**
     * Send error mail
     * @param array $error
     */
    public function handleFatalError(array $error)
    {
        if (! isset($_SERVER['DEVELOPER'])) {
        	$text = '<h1>' . $error['message'] . ' (' . $error['type'] . ')</h1>';
        	$text .= '<p>In ' . $error['file'] . ' (' . $error['line'] . ')</p>';
        	$text .= $this->getHtmlEnvParams();
    		
    		$html = new Mime\Part($text);
    		$html->type = 'text/html';
    		
    		$mimeBody = new Mime\Message();
    		$mimeBody->setParts(array($html));
    		
    		$mail = new Mail\Message();
    		$mail->setSubject('[' . $_SERVER['HTTP_HOST'] . '] ERROR: ' . $error['message']);
    		$mail->addTo('log@kofus.de');
    		$mail->setBody($mimeBody);
    		$mail->setFrom('log@kofus.de', 'Fehler-Manager');
    		
    		$transport = new Mail\Transport\Sendmail();
    		$transport->send($mail);
    	}
    }
    
    protected function exception2html(\Exception $e)
    {
    	$s = '<style>
			td {border: 1px solid gray}
			th {border: 1px solid gray}
			table {border-collapse: collapse}
		</style>';
    
    	$s .= '<h1>' . $e->getMessage() . ' (' . $e->getCode() . ')</h1>';
    	$s .= '<p>In ' . $e->getFile() . ' (' . $e->getLine() . ')</p>';
    
    	// Trace stack
    	$s .= '<h2>Trace-Stack</h2>';
    	$s .= '<table>';
    	foreach ($e->getTrace() as $line) {
    		$s .= '<tr>';
    		foreach (array('class', 'type', 'function', 'line', 'file') as $col) {
    			$s .= '<td>';
    			if (isset($line[$col])) {
    				if (is_array($line[$col])) {
    					$s .= implode(',', $line[$col]);
    				} else {
    					$s .= $line[$col];
    				}
    			}
    			$s .= '</td>';
    		}
    		$s .= '</tr>';
    	}
    	$s .= '</table>';
    
    	return $s;
    }
    
    protected function getHtmlEnvParams()
    {
        $s = '';
        $tables = array('Server-Daten' => $_SERVER, 'POST-Parameter' => $_POST, 'GET-Parameter' => $_GET);
        foreach ($tables as $title => $data) {
        	if (! $data) continue;
        	$s .= '<h2>' . $title . '</h2>';
        	$s .= '<table>';
        	foreach ($data as $key => $value) {
        		$s .= '<tr><th>'. $key . '</th><td>' . print_r($value, true) . '</td></tr>';
        	}
        	$s .= '</table>';
        }
        return $s;
    }
}