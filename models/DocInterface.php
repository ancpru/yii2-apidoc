<?php

namespace yii\apidoc\models;

/**
 * Base Interface for all doc elements
 * @author Andreas Prucha (Abexto - Helicon Software Development) <andreas.prucha@gmail.com>
 */
interface DocInterface
{
    /**
     * Normalizes the given name 
     * @param string|\phpDocumentor\Reflection\Fqsen $name to be used as key in the containing array
     */
    public static function normalizeKey($name);
    
    /**
     * Normalizes the name to be displayed
     * @param type $name
     */
    public static function normalizeName($name);
    
    /**
     * Returns the key of the element 
     * @return string 
     */
    public function getKey();
    
    /**
     * Returns the name of the element
     * For type elements like classes, interfaces and traits the FQN w/o leading
     * backslash should be returned. Local elements like properties, methods etc.
     * should return the name only
     * @return string Display Name
     */
    public function getName();
}
