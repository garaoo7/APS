<?php

class EntityRepository
{
    protected $CI;
    function __construct($dao = NULL,$cache = NULL,$model = NULL)
    {
        $this->CI = & get_instance();
    }
    
    protected function fillObjectWithData($object,$data)
    {
        if(is_array($data)) {
            $reflect = new ReflectionClass($object);
        
            foreach($data as $key => $value) {
                if($reflect->hasProperty($key)) {
                    //$reflectionProperty = $reflect->getProperty($key);
                    //$reflectionProperty->setAccessible(true);
                    //$reflectionProperty->setValue($object, $value);
                    $object->$key = $value;
                }
            }
            
            if(is_array($data['entities'])) {
                foreach($data['entities'] as $entityKey => $entity) {
                    if($reflect->hasProperty($entityKey)) {
                        //$reflectionProperty = $reflect->getProperty($entityKey);
                        //$reflectionProperty->setAccessible(true);
                        //$reflectionProperty->setValue($object, $entity);
                        $object->$entityKey = $entity;
                    }
                }
            }
        }
    }
}
