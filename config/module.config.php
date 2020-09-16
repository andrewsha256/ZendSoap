<?php
namespace Andrewsha256\ZendSoap;

use Zend\Router\Http\Segment;

return array(

	/*
	 * Routes
	 */
	'router' => array(
		'routes' => array(
			Controller\IndexController::class => array(
				'type' => Segment::class,
				'options' => array(
					'route' => '/services/soap[/][:serviceName[/]]',
					'defaults' => array(
						'controller' => Controller\IndexController::class,
						'action' => 'index',
					),
				),
			),
		),
	),


	/*
	 * Controllers
	 */
	'controllers' => array(
		'factories' => array(
			Controller\IndexController::class
					=> Controller\IndexControllerFactory::class,
		),
	),

	/**
	 * Module service
	 */
	'service_manager' => array(
		'factories' => array(
			SoapServiceManager::class
					=> \Zend\ServiceManager\Factory\InvokableFactory::class
		),
	),

	'view_manager' => array(
		'template_map' =>array(
			'andrewsha256/zend-soap/index/index'
					=> __DIR__ . '/../view/controller/index/index.phtml',
			'andrewsha256/zend-soap/index/404'
					=> __DIR__ . '/../view/controller/index/404.phtml',
			'andrewsha256/zend-soap/index/service-description'
					=> __DIR__ . '/../view/controller/index/service-description.phtml'
		),
	),
);