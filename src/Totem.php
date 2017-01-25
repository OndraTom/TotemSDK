<?php

namespace Totem\Sdk;

require_once(__DIR__ . '/TotemException.php');

/**
 * Totem API entry class.
 * 
 * @author oto
 */
final class Totem
{
	/**
	 * cURL options constants.
	 */
	const CONNECTION_TIMEOUT	= 10;
	const CONTENT_TYPE			= 'Content-Type: application/json';
	
	/**
	 * HTTP method constants.
	 */
	const ACTION_GET	= 'GET';
	const ACTION_INSERT = 'POST';
	const ACTION_UPDATE = 'PATCH';
	const ACTION_DELETE = 'DELETE';
	
	
	/**
	 * Users email.
	 * 
	 * @var string
	 */
	private $email;
	
	
	/**
	 * Users API key.
	 * 
	 * @var string
	 */
	private $apiKey;
	
	
	/**
	 * Totem API URL.
	 * 
	 * @var string
	 */
	private $apiUrl;
	
	
	/**
	 * cURL handler.
	 * 
	 * @var resource
	 */
	private $curlHandler;
	
	
	/**
	 * Allowed HTTP actions.
	 * 
	 * @var array
	 */
	private $allowedActions = [
		self::ACTION_GET,
		self::ACTION_INSERT,
		self::ACTION_UPDATE,
		self::ACTION_UPDATE
	];
	
	
	/**
	 * @param string $email
	 * @param string $apiKey
	 * @param string $apiUrl
	 */
	public function __construct($email, $apiKey, $apiUrl)
	{
		$this->email	= $email;
		$this->apiKey	= $apiKey;
		$this->apiUrl	= $apiUrl;
		
		$this->initCurlHandler();
	}
	
	
	/**
	 * Close the cURL resource when the object is destroyed.
	 */
	public function __destruct()
	{
		if (is_resource($this->curlHandler))
		{
			curl_close($this->curlHandler);
		}
	}
	
	
	/**
	 * Inits the cURL resource.
	 */
	private function initCurlHandler()
	{
		$this->curlHandler = curl_init();
		
		curl_setopt($this->curlHandler, CURLOPT_POST, true);
		curl_setopt($this->curlHandler, CURLOPT_HEADER, false);
		curl_setopt($this->curlHandler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curlHandler, CURLOPT_CONNECTTIMEOUT, self::CONNECTION_TIMEOUT);
		curl_setopt($this->curlHandler, CURLOPT_HTTPHEADER, array(self::CONTENT_TYPE));
		curl_setopt($this->curlHandler, CURLOPT_USERPWD, $this->email . ':' . $this->apiKey);
	}
	
	
	/**
	 * Calls the Totem API method and returns the JSON result.
	 * 
	 * @param	array	$getParams
	 * @param	string	$action
	 * @param	array	$postParams
	 * @return	string
	 * @throws	TotemException
	 */
	public function call(array $getParams, $action, array $postParams = [])
	{
		if (!in_array($action, $this->allowedActions))
		{
			throw new TotemException('Action ' . $action . ' is not allowed.');
		}
		
		curl_setopt($this->curlHandler, CURLOPT_URL, $this->apiUrl . '?' . http_build_query($getParams));
		curl_setopt($this->curlHandler, CURLOPT_POSTFIELDS, $postParams);
		curl_setopt($this->curlHandler, CURLOPT_CUSTOMREQUEST, $action);
		
		$result = curl_exec($this->curlHandler);
		$error	= curl_error($this->curlHandler);
		
		if ($error)
		{
			throw new TotemException('API call has failed: ' . $error);
		}
		
		return $result;
	}
}