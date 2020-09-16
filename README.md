# Zend SOAP Hub

SOAP service hub for Zend applications. Boilerplate for making SOAP services
with Zend 3.

Before going further make sure that you are familiar with
[Zend SOAP services](https://framework.zend.com/blog/2017-01-24-zend-soap-server.html).

Creating new SOAP service for Zend application is often associated with routine
and boring tasks.
In addition, there are some pitfalls that can waste some of your time.
E.g. like [this](http://stackoverflow.com/questions/29259305/soapserver-handle-ignoring-output-buffering).

This module tries to solve some of them.

## How to use

Register module in your Zend application (see `config/module.config.php`).

For every new SOAP service implement `Andrewsha256\ZendSoap\ServiceInterface`
and register it as new "Zend service" with name
`Andrewsha256\ZendSoap\ServiceInterface::class/<service_name>`.

`service_name` will be used as service URI.

For instance if you make implementation with `MyApp\PerfectSoapImpl` class and
register it as `Andrewsha256\ZendSoap\ServiceInterface::class/PerfectSoapImpl`
then this new SOAP service will be available at `/services/soap/PerfectSoapImpl`.

## Example

Let's make SOAP service that will have one method `divide` that will divide 2
operands (`lhs` and `rhs`) and return `result`. Let's throw an exception if
`rhs` is equal to `0`.

### Server class

```php
<?php
namespace MyApp;

use \Andrewsha256\ZendSoap\FaultCode;

class ServerImplementation
{
    public function __construct()
    {}

    public function divide(\stdClass $request)
    {
        if ($request->rhs === 0)
        {
            throw new \SoapFault(\Andrewsha256\ZendSoap\FaultCode::FAULT_CODE_DATA_SENDER, 'Dividing by zero');
        }
        return array(
            'result' => $request->lhs / $request->rhs
        );
    }
}
```

### ServiceInterface implementation

```php
<?php
namespace MyApp;

use \Zend\Soap\Server;

use \Andrewsha256\ZendSoap\ServiceInterface;

class ExampleSoapService implements ServiceInterface
{
    /** @const string */
    const VERSION = '1.0.0';

    /** @var \Zend\Uri\Uri */
    private $_uri;

    /** @var \Zend\Soap\Server */
    private $_server;

    /** @var string filepath to wsdl file */
    private $_wsdl;

    /** @var string */
    private $_title = '';

    /** @var string */
    private $_description = '';

    /**
     * @param string $wsdl
     * @param object $server
     * @param string $title
     * @param string $description
     */
    public function __construct($wsdl, $server, $title, $description = '')
    {
        $this->_server = new Server($wsdl);
        $this->_server->setObject($server);

        if(!file_exists($wsdl) || !is_readable($wsdl))
        {
            throw new \Exception('Unable to read WSDL file.');
        }
        $this->_wsdl = $wsdl;

        $this->_title  = $title;
        $this->_description = $description;
    }

    public function getVersion()
    {
        return static::VERSION;
    }

    public function setUri(\Zend\Uri\Uri $uri)
    {
        $this->_uri = $uri;
    }

    public function getUri()
    {
        return $this->_uri;
    }

    public function getWsdl()
    {
        return str_replace(
                '%URI%',
                $this->_uri->toString(),
                file_get_contents($this->_wsdl)
        );
    }

    public function getServer()
    {
        return $this->_server;
    }

    public function getExceptionsToRegister()
    {
        return array(
            \Zend\ServiceManager\Exception\ServiceNotFoundException::class,
            \Zend\ServiceManager\Exception\ServiceNotCreatedException::class,
            \SoapFault::class,
            \Exception::class
        );
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function getDescription()
    {
        if(empty($this->_description))
        {
            return file_get_contents(__DIR__ . '/README.md');
        }
        return $this->_description;
    }
}
```