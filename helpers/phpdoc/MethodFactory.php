<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2019 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidoc\helpers\phpdoc;

use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\Factory\AbstractFactory;
use phpDocumentor\Reflection\Php\Method as MethodDescriptor;

/**
 * Custom method factory
 * 
 * This Method factory implementation aims to work-around a possible bug in phpdocumentor/reflection
 * 
 * This factory actually works around this problem by calling the standard factory, but
 * resolving the result again.
 * 
 * See: [[ResolveNameNodeFixTrait]]
 * 
 * @todo Check regulary if this workaround is still necessary and get rid of this class if possible
 *
 * @author Andreas Prucha, Abexto - Helicon Software Development <andreas.prucha@gmail.com>
 */
class MethodFactory extends AbstractFactory implements ProjectFactoryStrategy
{
    
    use ResolveNameNodeFixTrait;

    /**
     * @var \phpDocumentor\Reflection\Php\Factory\Method
     */
    private $standardMethodFactory = null;

    public function __construct()
    {
        $this->standardMethodFactory = new \phpDocumentor\Reflection\Php\Factory\Method();
    }

    /**
     * @param type $object
     * @param \phpDocumentor\Reflection\Php\StrategyContainer $strategies
     * @param \phpDocumentor\Reflection\Types\Context|null $context
     * @return MethodDescriptor
     */
    protected function doCreate($object, \phpDocumentor\Reflection\Php\StrategyContainer $strategies, ?\phpDocumentor\Reflection\Types\Context $context = null)
    {
        $returnTypeNode = $object->getReturnType();
        $result = $this->standardMethodFactory->create($object, $strategies, $context);
        if ($result instanceof MethodDescriptor && isset($returnTypeNode)) {
            $originalResult = $result;
            $result = new MethodDescriptor(
                    $originalResult->getFqsen(),
                    $originalResult->getVisibility(),
                    $originalResult->getDocBlock(),
                    $originalResult->isAbstract(),
                    $originalResult->isStatic(),
                    $originalResult->isFinal(),
                    $originalResult->getLocation(),
                    $this->resolveNode($returnTypeNode, $context));
            foreach ($originalResult->getArguments() as $arg) {
                $result->addArgument($arg);
            }
        }
        return $result;
    }

    public function matches($object): bool
    {
        return $this->standardMethodFactory->matches($object);
    }

}
