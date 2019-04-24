<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidoc\models;

use phpDocumentor\Reflection\DocBlock\Tags\Var_ as VarTag;
use yii\apidoc\helpers\PrettyPrinter;

/**
 * Represents API documentation information for a `property`.
 *
 * @property bool $isReadOnly If property is read only. This property is read-only.
 * @property bool $isWriteOnly If property is write only. This property is read-only.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class PropertyDoc extends BaseDoc implements DocInterface
{
    use LocalDocElementTrait {
        LocalDocElementTrait::normalizeName as defaultNormalizeName;
    }
    
    public $visibility;
    public $isStatic;
    public $type;
    public $types;
    public $defaultValue;
    // will be set by creating class
    public $getter;
    public $setter;
    // will be set by creating class
    public $definedBy;

    
    /**
     * Normalizes the display name
     * Properties are prefixed with "$"
     * @param mixed $name
     * @return string
     */
    public static function normalizeName($name) 
    {
        $result = static::defaultNormalizeName($name);
        if ($result[0] !== '$') {
            $result = '$' . $result;
        }
        return $result;
    }
    
    /**
     * @return bool if property is read only
     */
    public function getIsReadOnly()
    {
        return $this->getter !== null && $this->setter === null;
    }

    /**
     * @return bool if property is write only
     */
    public function getIsWriteOnly()
    {
        return $this->getter === null && $this->setter !== null;
    }

    /**
     * @param \phpDocumentor\Reflection\Php\Property $reflector
     * @param Context $context
     * @param array $config
     */
    public function __construct(?\phpDocumentor\Reflection\Php\Property $reflector = null, $context = null, $config = [])
    {
        parent::__construct($reflector, $context, $config);

        if ($reflector === null) {
            return;
        }

        $this->visibility = $reflector->getVisibility();
        $this->isStatic = $reflector->isStatic();

        // bypass $reflector->getDefault() for short array syntax
        if ($reflector->getDefault()) {
            /* @todo reflector seems not to provide parsing nodes - find a way to workaround this problem in order
             * to use pretty printer again
             */
            if (is_string($reflector->getDefault())) {
                $this->defaultValue = $reflector->getDefault(); // We do not have parser nodes. Does this work??? 
            } else {
                $this->defaultValue = PrettyPrinter::getRepresentationOfValue($reflector->getDefault());
            }
        }

        $hasInheritdoc = false;
        foreach ($this->tags as $tag) {
            if ($tag->getName() === 'inheritdoc') {
                $hasInheritdoc = true;
            }
            if ($tag instanceof VarTag) {
                $this->type = $tag->getType();
                $this->types = $tag->getType();
                $this->description = static::mbUcFirst($tag->getDescription());
                $this->shortDescription = BaseDoc::extractFirstSentence($this->description);
            }
        }
        if (empty($this->shortDescription) && $context !== null && !$hasInheritdoc) {
            $context->warnings[] = [
                'line' => $this->startLine,
                'file' => $this->sourceFile,
                'message' => "No short description for element '{$this->name}'",
            ];
        }
    }
}
