<?php

namespace Kofus\System\Log\Writer;

use Traversable;
use Zend\Db\Adapter\Adapter;
use Zend\Log\Exception;


class Soap extends \Zend\Log\Writer\AbstractWriter
{
    
    
    protected $soapClient;
    
    protected $clientOptions = array();
    
    protected $logParamsQuery = false;
    protected $logParamsPost = false;
    protected $logParamsServer = false;
    protected $logParamsSession = false;
    protected $paramsCustom = array();
    
    public function __construct($options = null)
    {
        parent::__construct($options);
        
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        }
        
        if (is_array($options)) {
            if (isset($options['client'])) {
                $this->clientOptions = $options['client'];
            }
            $this->logParamsQuery = isset($options['log_params_query']);
            $this->logParamsPost = isset($options['log_params_post']);
            $this->logParamsServer = isset($options['log_params_server']);
            $this->logParamsSession = isset($options['log_params_session']);
            if (isset($options['params_custom']))
                $this->paramsCustom = $options['params_custom'];
        }
        
    }


    /**
     * Remove reference to database adapter
     *
     * @return void
     */
    public function shutdown()
    {
        $this->soapClient = null;
    }

    /**
     * Write a message to the log.
     *
     * @param array $event event data
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function doWrite(array $event)
    {
        if ($this->logParamsServer && isset($_SERVER))
            $event['params_server'] = $_SERVER;
        if ($this->logParamsQuery && isset($_GET))
            $event['params_query'] = $_GET;
        if ($this->logParamsPost && isset($_POST))
            $event['params_post'] = $_POST;
        if ($this->logParamsSession && isset($_SESSION))
            $event['params_session'] = $_SESSION;
                
        if (isset($this->paramsCustom))
            $event['params_custom'] = $this->paramsCustom;
        
        $event['timestamp'] = $event['timestamp']->format('Y-m-d H:i:s');
            
        
        $this->getClient()->log($event);
    }
    
    protected function getClient()
    {
        if (! $this->soapClient) {
            $wsdl = null;
            if (isset($this->clientOptions['wsdl']))
                $wsdl = $this->clientOptions['wsdl'];
            $this->soapClient = new \SoapClient($wsdl, $this->clientOptions);
            
                
            if (isset($this->clientOptions['uri']) && isset($this->clientOptions['passphrase'])) {
                $authHeader = new \stdClass();
                $authHeader->passphrase = $this->clientOptions['passphrase'];
                $headers = array(
                    new \SoapHeader($this->clientOptions['location'], 'AuthHeader', $authHeader)
                );
                $this->soapClient->__setSoapHeaders($headers);
            }
        }
        return $this->soapClient;
    }
    
    


}
