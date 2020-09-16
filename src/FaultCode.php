<?php

namespace Andrewsha256\ZendSoap;

/**
 * SOAP errors
 */
class FaultCode
{
	/**#@+
	 * Fault codes
	 */
	const FAULT_CODE_VERSION_MISMATCH = 'VersionMismatch';
	const FAULT_CODE_MUST_UNDERSTAND  = 'MustUnderstand';
	const FAULT_CODE_DATA_ENCODING_UNKNOWN = 'DataEncodingUnknown';
	const FAULT_CODE_DATA_SENDER = 'Sender';
	const FAULT_CODE_RECEIVER    = 'Receiver';
	const FAULT_CODE_SERVER      = 'Server';
	/**#@-*/

	/**
	 * @var array classes cache with class name as key
	 */
	private static $_refCache = array();

	/**
	 * @var mixed current value
	 */
	private $_value = 'Receiver';

	/**
	 * @param string $value
	 */
	public function __construct($value)
	{
		$this->setValue($value);
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * @param  mixed $value
	 * @return void
	 * @throws \Exception
	 */
	public function setValue($value)
	{
		if( ! static::isValid($value) )
		{
			$className = get_called_class();
			throw new \Exception("Fault code value \"{$value}\" is not valid for \"{$className}\"");
		}
		$this->_value = $value;
	}

	/**
	 * @param  mixed $value
	 * @return bool
	 */
	public function isValid($value)
	{
		return in_array($value, static::_getConstants());
	}

	/**
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->_value;
	}

	/**
	 * @return array
	 */
	protected static function _getConstants()
	{
		$className = get_called_class();

		if( ! isset(self::$_refCache[$className]) )
		{
			$reflect = new \ReflectionClass($className);
			self::$_refCache[$className] = $reflect->getConstants();
		}

		return self::$_refCache[$className];
	}
}
