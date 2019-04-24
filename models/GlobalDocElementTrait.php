<?php


namespace yii\apidoc\models;

use phpDocumentor\Reflection\Fqsen;

/**
 * Default implementation for "local" elements
 * @author Andreas Prucha (Abexto - Helicon Software Development) <andreas.prucha@gmail.com>
 */
trait GlobalDocElementTrait
{
     /**
     * Normalizes the given name to FQSEN
     * @param string|Fqsen $name to be used as key in the containing array
     * @return string
     */
    public static function normalizeKey($name)
    {
        return (string)$name;
    }
    
    /**
     * Normalizes the name to be displayed
     * Global elements are displayed with namespace, but with leading backspace removed
     * @param type $name
     */
    public static function normalizeName($name)
    {
        return ltrim((string)$name, '\\');
    }

}
