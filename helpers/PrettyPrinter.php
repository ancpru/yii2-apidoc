<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidoc\helpers;

use PhpParser\Node\Expr;

/**
 * Enhances the phpDocumentor PrettyPrinter with short array syntax
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class PrettyPrinter extends \phpDocumentor\Reflection\PrettyPrinter
{
    /**
     * Constructor
     * @param type $options
     */
    public function __construct(array $options = array())
    {
        $options = array_merge(['shortArraySyntax' => true], $options);
        parent::__construct($options);
    }
    
    /**
     * @param Expr\Array_ $node
     * @return string
     */
    public function __pExpr_Array(Expr\Array_ $node)
    {
        return '[' . $this->pCommaSeparated($node->items) . ']';
    }
    
    /**
     * Returns a simple human readable output for a value.
     *
     * @param Expr $value The value node as provided by PHP-Parser.
     * @return string
     */
    public static function getRepresentationOfValue(Expr $value)
    {
        if ($value === null) {
            return '';
        }

        $printer = new static();

        return $printer->prettyPrintExpr($value);
    }
}
