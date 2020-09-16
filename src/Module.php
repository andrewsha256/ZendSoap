<?php

namespace Andrewsha256\ZendSoap;

/**
 * Module for all further services
 */
class Module implements \Zend\ModuleManager\Feature\ConfigProviderInterface
{
	const VERSION = '1.0.0';

	/**
	 * Returns all module autoloaders
	 * 
	 * @return array
	 */
	public function getAutoloaderConfig()
	{
		if(!class_exists(\Andrewsha256\ZendSoap\ServiceAbstract::class))
		{
			return array(
				'Zend\Loader\StandardAutoloader' => array(
					'namespaces' => array(
						__NAMESPACE__ => __DIR__,
					),
				),
			);
		}
		return array();
	}

	public function getConfig()
	{
		return include __DIR__ . '/../config/module.config.php';
	}
}
