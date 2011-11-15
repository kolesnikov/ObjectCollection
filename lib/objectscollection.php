<?php
/**
 * Copyright (c) 2011 Eugene Kolesnikov
 * 
 * @author Eugene Kolesnikov
 * @version 0.0.1
 * 
 */


/**
 * Коллекция транзакций
 */
Class ObjectsCollection
{
	protected $__storage = array();
	
	private $classVars;
	private $classMethods;
	
	private $errors = NULL;
	private $lastError = NULL;


	/**
	 *
	 * @param string $className 
	 */
	function __construct($className)
	{
		if (class_exists($className))
		{
			$this->classVars	= get_class_vars($className);
			$this->classMethods	= get_class_methods($className);
		}
		else
		{
			throw new Exception('The "'.$className.'" is not class');
		}
	}
	
	
    /**
	 * Добавляет транзакцию в коллекцию
	 * 
	 * @param Transaction $transaction Объект транзакции
	 */
	public function push($object)
	{
		if (is_object($object))
		{
			$this->__storage[spl_object_hash($object)] = $object;
		}
	}
    
	
	/**
	 *
	 * @param type $name
	 * @return type 
	 */
    function __get($name)
    {
        if (isset($this->$name)) return $this->$name;
			
		if (!array_key_exists($name, $this->classVars))
			throw new InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $name));
		
        $callback	= function($object) use ($name) { return $object->$name; };
        
        return $this->map($callback);
    }
    
    
	/**
	 *
	 * @param type $name
	 * @param type $value
	 * @return type 
	 */
    function __set($name, $value)
    {
        if (isset($this->$name)) return $this->$name = $value;
        
		if (!array_key_exists($name, $this->classVars))
			throw new InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $name));
		
        $callback	= function($object) use ($name, $value) { $object->$name = $value; };
        
        return $this->map($callback);
    }
	
	
	/**
	 *
	 * @param type $name
	 * @param type $arguments
	 * @return type 
	 */
	function __call($name, $arguments)
	{
		if (method_exists($this, $name)) call_user_func_array ($this->$name, $arguments);
		
		if (!in_array($name, $this->classMethods))
			throw new InvalidArgumentException(sprintf('Class "%s" is not defined.', $name));
		
		$callback	= function($object) use ($name, $arguments){
			return call_user_func_array(array($object, $name), $arguments);
		};
		
		return $this->map($callback);
	}
    
    
	/**
	 * Метод выполняет переданную операцию для каждого элемента коллекции
	 * @param type $callback
	 * @return type 
	 */
	private function map(Closure $callback)
	{
        $this->errors   = $this->lastError  = NULL;

        $result = array_map($callback, $this->__storage);
        
        foreach ($result as $item)
            if ($item instanceof Exception) $this->errors[]    = $item;
            
        if (is_array($this->errors))
        {
            $this->lastError    = end($this->errors);
            throw new Exception('Ошибка выполнения коллекции');
        }
        
        return $result;
	}
}
