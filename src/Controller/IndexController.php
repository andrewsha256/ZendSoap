<?php

namespace Andrewsha256\ZendSoap\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use \Andrewsha256\ZendSoap\ServiceInterface;

/**
 * Root / route controller for all services
 *
 * One point for all client requests. Parses request, searches for requested
 * SOAP service, call it, handles response (and errors) and sends response to
 * client.
 *
 * Handles POST query as SOAP call, and GET query as description request. If GET
 * request has parameter `?wsdl` returns service's WSDL.
 *
 * @see https://framework.zend.com/blog/2017-01-24-zend-soap-server.html
 */
class IndexController extends AbstractActionController
{
	/** @var \Interop\Container\ContainerInterface */
	private $_sm;

	/**
	 * @param \Interop\Container\ContainerInterface $sm
	 */
	public function __construct(\Interop\Container\ContainerInterface $sm)
	{
		$this->_sm = $sm;
	}

	/**
	 * @return \Zend\Http\Response
	 */
	public function indexAction()
	{

		/** @var \Zend\Http\Request $request */
		$request = $this->getRequest();

		/** @var string $serviceName */
		$serviceName = $this->params('serviceName');

		if(is_null($serviceName))
		{
			// service name missing - prints Hello World page
			return parent::indexAction();
		}
		else
		{
			// service requested via name
			$serviceRealName = ServiceInterface::class . '/' . $serviceName;

			if(!$this->_sm->has($serviceRealName))
			{
				// service not found
				return $this->_error404();
			}

			// Service initialization

			/** @var \Andrewsha256\ZendSoap\ServiceInterface */
			$service = $this->_sm->get($serviceRealName);

			// service URI
			$service->setUri(new \Zend\Uri\Uri($this->url()->fromRoute(
					self::class, array(), array('force_canonical' => true)
			) . '/' . $serviceName . '/'));

			// Registering service possible exceptions.
			// They will be send to user as SoapFault
			$service->getServer()
					->registerFaultException($service->getExceptionsToRegister());

			if(!in_array(ServiceInterface::class, class_implements($service)))
			{
				// $service is not a SOAP service implementation
				return $this->_errorResponse(500, array(), 'Service doesn\'t implement required interface.');
			}

			if($request->isPost())
			{
				// POST query handled as SOAP-call
				return $this->_handleSoap($service->getServer());
			}
			else if($request->isGet())
			{
				// GET requests handled as request for description
				/** @var query \Zend\Stdlib\Parameters $query */
				$query = $request->getQuery();

				if(isset($query['wsdl']))
				{
					return $this->_handleWsdl($service->getWsdl());
				}

				/** @var \Zend\View\Model\ViewModel $view */
				$view = new ViewModel;
				$view->setTemplate('andrewsha256/zend-soap/index/service-description')
						->setVariable('service', $service);
				return $view;
			}
			else
			{
				return $this->_errorResponse(405, array('Allow' => 'GET, POST'));
			}
		}
	}

	/**
	 * Makes SOAP-call
	 *
	 * This methods wraps SOAP call and handles errors. This methods solves
	 * some strange issues with missed `ob_end_clean()` call.
	 * http://stackoverflow.com/questions/29259305/soapserver-handle-ignoring-output-buffering
	 * 
	 * @param \Zend\Soap\Server $server
	 * @return \Zend\Http\Response
	 */
	private function _handleSoap(\Zend\Soap\Server $server)
	{
		/** @var \Zend\Http\Response $response */
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('Content-Type', 'application/soap+xml');

		$server->setReturnResponse(true);

		$soapResponse = $server->handle($this->getRequest()->getContent());

		if(!($soapResponse instanceof \SoapFault))
		{
			$response->setContent($soapResponse);
			return $response;
		}

		// Handles unhandled error and forces shutdown of whole script to solve
		//problem with missing ob_end_clean()
		$server->getSoap()->fault($soapResponse->faultcode, $soapResponse->getMessage());
	}

	/**
	 * Returns representation WSDL as \Zend\Http\Response
	 *
	 * @param  \Zend\Soap\Wsdl|string $wsdl
	 * @return \Zend\Http\Response
	 */
	private function _handleWsdl($wsdl)
	{
		/** @var \Zend\Http\Response $response */
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('Content-Type', 'application/wsdl+xml');

		if(is_string($wsdl))
		{
			$response->setContent($wsdl);
		}
		else
		{
			$response->setContent($service->getWsdl()->toXML());
		}
		return $response;
	}

	/**
	 * Returns 404 error
	 * 
	 * @return \Zend\Http\Response
	 */
	private function _error404()
	{
		/** @var \Zend\Http\Response $response */
		$response = $this->getResponse();
		$response->setStatusCode(404);

		/** @var \Zend\View\Model\ViewModel $view */
		$view = new ViewModel;
		$view->setTemplate('andrewsha256/zend-soap/index/index/404');
		return $view;
	}

	/**
	 * Returns errors
	 * 
	 * @param  int    $code
	 * @param  array  $headers
	 * @param  string $message
	 * @return \Zend\Http\Response
	 */
	private function _errorResponse($code = 500, $headers = array(), $message)
	{
		$response = $this->getResponse();
		$response->setStatusCode($code);
		foreach($headers as $name => $value)
		{
			$response->getHeaders()->addHeaderLine($name, $value);
		}
		if(is_string($message) && !empty($message))
		{
			$response->setContent($message);
		}
		return $response;
	}
}
