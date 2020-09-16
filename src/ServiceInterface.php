<?php
namespace Andrewsha256\ZendSoap;

/**
 * This interface should be implemented by "SOAP-server class"
 */
interface ServiceInterface
{
	/**
	 * Returns service version
	 * 
	 * @return string
	 */
	public function getVersion();

	/**
	 * Sets service URI
	 * 
	 * @param \Zend\Uri\Uri $uri
	 * @return void
	 */
	public function setUri(\Zend\Uri\Uri $uri);

	/**
	 * Returns service URI
	 * 
	 * @return \Zend\Uri\Uri $uri
	 */
	public function getUri();

	/**
	 * Returns \Zend\Soap\Server object for SOAP service
	 * 
	 * @return \Zend\Soap\Server
	 */
	public function getServer();

	/**
	 * Returns service WSDL
	 * 
	 * @return \Zend\Soap\Wsdl
	 */
	public function getWsdl();

	/**
	 * Returns array of exceptions that can be thrown by service
	 * 
	 * @return string|array
	 */
	public function getExceptionsToRegister();

	/**
	 * Returns service title that will be shown to client
	 * 
	 * @return string
	 */
	public function getTitle();

	/**
	 * Returns service description that will be shown to client
	 * 
	 * @return \Zend\Http\Response|string
	 */
	public function getDescription();

}
