<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidoc\models;

use phpDocumentor\Reflection\DocBlock\Tags\Param as ParamTag;
use phpDocumentor\Reflection\DocBlock\Tags\Property as PropertyTag;
use phpDocumentor\Reflection\DocBlock\Tags\Return_ as ReturnTag;
use phpDocumentor\Reflection\DocBlock\Tags\Throws as ThrowsTag;
use phpDocumentor\Reflection\Php\FunctionReflector;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Compound as CompoundType;
use phpDocumentor\Reflection\Types\Mixed_ as MixedType;
use phpDocumentor\Reflection\Types\Void_ as VoidType;

/**
 * Represents API documentation information for a `function`.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class FunctionDoc extends BaseDoc
{
    use GlobalDocElementTrait;
    /**
     * @var ParamDoc[]
     */
    public $params = [];
    public $exceptions = [];
    public $return;
    public $returnType;
    public $returnTypes;
    public $isReturnByReference;


    /**
     * @param FunctionReflector $reflector
     * @param Context $context
     * @param array $config
     */
    public function __construct($reflector = null, $context = null, $config = [])
    {
        parent::__construct($reflector, $context, $config);

        if ($reflector === null) {
            return;
        }

        /* @todo find out if there is a new way to check on ref (nothing found) 
         Previous was:
        $this->isReturnByReference = $reflector->getReturnType()-> isByRef();
        */
        $this->isReturnByReference = false;

        foreach ($reflector->getArguments() as $arg) {
            $arg = new ParamDoc($arg, $context, ['sourceFile' => $this->sourceFile]);
            $this->params[$arg->getKey()] = $arg;
        }

        foreach ($this->tags as $i => $tag) {
            if ($tag instanceof ThrowsTag) {
                $this->exceptions[(string)$tag->getType()] = $tag->getDescription();
                unset($this->tags[$i]);
            } elseif ($tag instanceof PropertyTag) {
                // ignore property tag
            } elseif ($tag instanceof ParamTag) {
                $paramName = $tag->getVariableName();
                if (!isset($this->params[$paramName]) && $context !== null) {
                    $context->errors[] = [
                        'line' => $this->startLine,
                        'file' => $this->sourceFile,
                        'message' => "Undefined parameter documented: $paramName in {$this->name}().",
                    ];
                    continue;
                }
                $this->params[$paramName]->description = static::mbUcFirst($tag->getDescription());
                $this->params[$paramName]->type = $tag->getType();
                $this->params[$paramName]->types = $tag->getType(); // Compound implements array
                unset($this->tags[$i]);
            } elseif ($tag instanceof ReturnTag) {
                $this->returnType = $tag->getType();
                $this->returnTypes = $tag->getType(); // Compound implements array
                $this->return = static::mbUcFirst($tag->getDescription());
                unset($this->tags[$i]);
            }
        }

        // Add the declared return types to the return types specified by tag
        $returnType = $reflector->getReturnType();
        if (!$this->returnType) {
            // No return type specified by @return, yet, thus we use the function
            // retun type provided by the reflector.
            // As of now apidoc assumes a void result if docblock does not contain
            // a result tag, but reflector assumes Mixed as default result type.
            // In order to stay consistent with the convention of having void as
            // result we use Void.
            if ($returnType instanceof MixedType) {
                $this->returnType = new VoidType();
                $this->returnTypes = $this->returnType;
            } else {
                $this->returnType = $returnType;
                $this->returnTypes = $returnType;
            }
        } else {
            // Return types are already specified by @return. In order to ensure
            // complete documentation the return type provided by reflector is
            // added unless it's Mixed
            if (!$this->returnTypes instanceof CompoundType) {
                $this->returnTypes = new CompoundType([$this->returnTypes]);
            }
            if (!$returnType instanceof MixedType) {
                $this->returnType = $returnType;
                $found = false;
                foreach ($this->returnTypes as $t) {
                    if ((string)$t = (string)$returnType) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $this->returnTypes->getIterator()->append($returnType);
                }
            }
        }
    }
}
