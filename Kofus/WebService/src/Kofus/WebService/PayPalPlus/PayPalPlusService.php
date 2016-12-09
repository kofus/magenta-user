<?php

namespace Kofus\Webservice\PayPalPlus;

use Zend\Http\Header;
use Zend\Http\Headers;
use Zend\Json\Json;
use Zend\Http\Client;
use Commerce\Entity\CartEntity;
use Kofus\System\Service\AbstractService;


class PayPalPlusService extends AbstractService
{
    /**
     * Perform remote api call
     * @param string $method
     * @param string $resource
     * @param array $params
     * @throws \Exception
     * @return stdClass
     */
    public function api($method, $resource, array $params=array())
    {
        $account = $this->getAccountConfig();
        
        $client = $this->getHttpClient();
        $client->setUri($account['api_uri'] . '/' . $resource);
        $headers = new Headers();
        $headers->addHeader(new Header\Authorization('Bearer ' . $this->getAccessToken()));
        $headers->addHeader(new Header\ContentType('application/json'));
        $client->setHeaders($headers);
        if ($params)
        	$client->setRawBody(Json::encode($params));
        $client->setMethod($method);
        $response = $client->send();
        $archive = $this->getServiceLocator()->get('KofusArchive');
        $archive->http('PayPalPlus')->add($client);
        
        if ($response->getStatusCode() >= 300)
            throw new \Exception('PayPal API Exception');
        $body = $response->getBody();
       	if ($body)
        	return Json::decode($response->getBody());
    }
    
    protected static $CACHE_KEY = 'Kofus.WebService.PayPalPlus.AccessToken';
    
    
    public function getAccessToken()
    {
        $cache = $this->getServiceLocator()->get('Cache');
        
        if ($cache->hasItem(self::$CACHE_KEY)) {
            $obj = unserialize($cache->getItem(self::$CACHE_KEY));
            if (time() < $obj->expires_at)
                return $obj->access_token;
        }
        
        $account = $this->getAccountConfig();
        
        $client = $this->getHttpClient();
        $client->setMethod('POST');
        $client->setUri($account['oauth']['uri']);
        $client->setParameterPost(array('grant_type' => 'client_credentials'));
        $client->setAuth($account['oauth']['client_id'], $account['oauth']['secret']);
        $headers = new \Zend\Http\Headers();
        $headers->addHeader(new Header\ContentType('application/x-www-form-urlencoded'));
        $headers->addHeader(new Header\Accept('application/json'));
        $response = $client->send();
        $archive = $this->getServiceLocator()->get('KofusArchive');
        $archive->http('PayPalPlus')->add($client);
        $obj = Json::decode($response->getBody());
        $obj->expires_at = $obj->expires_in + time();
        $cache->setItem(self::$CACHE_KEY, serialize($obj));
        return $obj->access_token;
        
    }
    
    public function getAccountConfig()
    {
    	$enabled = $this->config()->get('webservice.ppplus.accounts.enabled');
    	if (! $enabled)
    		throw new \Exception('No enabled PayPalPlus account found in config');
    	$config = $this->config()->get('webservice.ppplus.accounts.available.'.$enabled);
    	if (! $config)
    		throw new \Exception('PayPalPlus account not found in config: ' . $enabled);
    	return $config;
    }
    
    public function createPayment(\Commerce\Entity\CartEntity $cart, array $requestParams=array())
    {
    	// Build items
    	$items = array();
    	foreach ($cart->getLineItems('product') as $li) {
    		$items[] = array(
    				'name' => $li->getLabel(),
    				'price' => $li->getPrice()->getValue(),
    				'currency' => $li->getPrice()->getCurrency(),
    				'quantity' => $li->getQuantity()
    		);
    	}
    	
    	// Build payment request
    	$shipping = $cart->getAddress('shipping');
    	$invoice = $cart->getAddress('invoice');
    	
    	$total = $cart->getTotal();
    	$enabled = $this->config()->get('webservice.ppplus.accounts.enabled');
    	$experienceId = $this->config()->get('webservice.ppplus.accounts.available.' . $enabled . '.experience_id');
    	
    	
    	
    	$_requestParams = array(
    			'intent' => 'sale',
    			'experience_profile_id' => $experienceId,
    			'payer' => array(
    					'payment_method' => 'paypal',
    					'payer_info' => array(
    							//'email' => 'bernhardt@kofus.de',
    							//'first_name' => 'Ingo',
    							//'last_name' => 'Bernhardt',
    							'billing_address' => array(
    									'line1' => $invoice->getStreet(),
    									'line2' => $invoice->getAdditional1(),
    									'city' => $invoice->getCity(),
    									'country_code' => $invoice->getCountry(),
    									'postal_code' => $invoice->getPostCode()
    							),
    							'shipping_address' => array(
    									"recipient_name" => $shipping->getRecipient(),
    									"line1" => $shipping->getStreet(),
    									"line2" => $shipping->getAdditional1(),
    									"city" => $shipping->getCity(),
    									"postal_code" => $shipping->getPostCode(),
    									"country_code" => $shipping->getCountry()
    							)
    					)
    			),
    			'transactions' => array(
    					array(
    							'amount' => array('total' => number_format($total->getValue(), 2), 'currency' => $total->getCurrency()),
    							/*
    							 'item_list' => array(
    							 		'items' => $items,
    							 ),
    			*/
    					),
    			),
    	    		
    	);
    	
    	$requestParams = array_merge_recursive($requestParams, $_requestParams);
    	
    	return $this->api('POST', 'v1/payments/payment', $requestParams);    	
    }
    
    protected function getHttpClient()
    {
    	$client = new Client();
    	if ($this->config()->get('webservice.ppplus.http_client_options', array()))
    		$client->setOptions($this->config()->get('webservice.ppplus.http_client_options', array()));
    	return $client;
    }
    
    public function executePayment($cart)
    {
        $payerId = $cart->getParam('ppplus.payer_id');
        $paymentId = $cart->getParam('ppplus.payment_id');
        
        return $this->api('POST', 'v1/payments/payment/' . $paymentId . '/execute', array(
        	'payer_id' => $payerId
        ));
        
    }
    
    
    
    

    
    
    
    
	
	
}