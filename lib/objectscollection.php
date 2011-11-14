<?php

/**
 * Коллекция транзакций
 */
Class ObjectsCollection
{
	
	protected $__storage = array();
    
    private $errors = NULL;
    private $lastError = NULL;

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
        
        $callback	= function($object) use ($name)
        {
            if (isset ($object->$name)) {
                return $object->$name;
            }
            else {
                return new InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $name));
            }
        };
        
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
        
        $callback	= function($object) use ($name, $value)
        {
            if (isset ($object->$name)) {
                $object->$name = $value;
            }
            else {
                return new InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $name));
            }
        };
        
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
		
		$callback	= function($object) use ($name, $arguments){
			return call_user_func_array($object->$name, $arguments);
		};
		
		$this->map($callback);
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
