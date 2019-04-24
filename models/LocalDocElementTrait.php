<?php


namespace yii\apidoc\models;

use phpDocumentor\Reflection\Fqsen;

/**
 * Default implementation for global elements like types (classes, interfaces traits, functions)
 * @author Andreas Prucha (Abexto - Helicon Software Development) <andreas.prucha@gmail.com>
 */
trait LocalDocElementTrait
{
     /**
     * Normalizes the given name to FQSEN
     * @param string|Fqsen $name to be used as key in the containing array
     * @return string
     */
    public static function normalizeKey($name)
    {
        if (!$name instanceof Fqsen) {
            if (strpos($name, '\\') || strpos($name, ':')) {
                $name = new \phpDocumentor\Reflection\Fqsenen($name);
            }
        }
        $result = ($name instanceof Fqsen) ? $name->getName() : $name;
        return rtrim(ltrim($result, '$ '), '() ');
    }
    
    /**
     * Normalizes the name to be displayed
     * In a local context just the name part is displayed (i.E. without namespace and class)
     * @param type $name
     */
    public static function normalizeName($name)
    {
        if (!$name instanceof Fqsen) {
            $name = new Fqsen($name);
        }
        return $name->getName();
    }

}
