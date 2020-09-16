<?php
namespace Andrewsha256\ZendSoap\Controller;

use \Zend\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
	/**
	 * @param \Interop\Container\ContainerInterface $container
	 * @param string $requestedName
	 * @param array $options
	 * @return \Andrewsha256\ZendSoap\Controller\IndexController
	 */
	public function __invoke(\Interop\Container\ContainerInterface $container,
			$requestedName, array $options = NULL)
	{
		return new IndexController($container);
	}

}
