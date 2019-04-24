<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidoc\models;

use yii\apidoc\helpers\PrettyPrinter;
use yii\base\BaseObject;

/**
 * Represents API documentation information for a [[FunctionDoc|function]] or [[MethodDoc|method]] `param`.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class ParamDoc extends BaseObject implements DocInterface
{
    private $_name;
    public $typeHint;
    public $isOptional;
    public $isVariadic; 
    public $defaultValue;
    public $isPassedByReference;
    // will be set by creating class
    public $description;
    public $type;
    public $types;
    public $sourceFile;


    /**
     * @param $reflector
     * @param Context $context
     * @param array $config
     */
    public function __construct(\phpDocumentor\Reflection\Php\Argument $reflector = null, $context = null, $config = [])
    {
        parent::__construct($config);

        if ($reflector === null) {
            return;
        }

        $this->_name = $reflector->getName();
        $this->typeHint = $reflector->getType();
        $this->isOptional = $reflector->getDefault() !== null;
        $this->isVariadic = $reflector->isVariadic();

        // bypass $reflector->getDefault() for short array syntax
        if ($reflector->getDefault()) {
            /* @todo reflector seems not to provide parsing nodes - find a way to workaround this problem in order
             * to use pretty printer again
             */
            if (is_string($reflector->getDefault())) {
                $this->defaultValue = $reflector->getDefault();
            } else {
                $this->defaultValue = PrettyPrinter::getRepresentationOfValue($reflector->getDefault());
            }
        }
        $this->isPassedByReference = $reflector->isByReference();
    }

    public function getKey(): string
    {
        return static::normalizeKey($this->_name);
    }

    public static function normalizeKey($name)
    {
        return $name;
    }

    public static function normalizeName($name)
    {
        return '$'.$name;
    }

    public function getName(): string
    {
        return static::normalizeName($this->_name);
    }

}
