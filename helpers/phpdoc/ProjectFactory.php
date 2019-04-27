<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2019 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidoc\helpers\phpdoc;

use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactory as StandardProjectFactory;
use phpDocumentor\Reflection\Project;
use phpDocumentor\Reflection\ProjectFactory as ProjectFactoryInterface;
use yii\apidoc\helpers\PrettyPrinter;

/**
 * Description of ProjectFactory
 *
 * @author Andreas Prucha (Abexto - Helicon Software Development) <andreas.prucha@gmail.com>
 */
class ProjectFactory implements ProjectFactoryInterface
{

    /**
     *
     * @var ProjectFactory
     */
    private $standardProjectFactory = null;

    /**
     * @see StandardProjectFactory::__construct($strategies)
     */
    public function __construct(array $strategies)
    {
        $this->standardProjectFactory = new StandardProjectFactory($strategies);
    }

    /**
     * Creates a new instance of this factory. With all default strategies.
     */
    public static function createInstance(): self
    {
        return new static(
                [
            new Factory\Argument(new PrettyPrinter()),
            new Factory\Class_(),
            new Factory\Constant(new PrettyPrinter()),
            new Factory\DocBlock(DocBlockFactoryFactory::createInstance([
                        'event' => EventTag::class,
                        'see' => SeeTag::class
                    ])),
            new Factory\File(NodesFactory::createInstance()),
            new Factory\Function_(),
            new Factory\Interface_(),
            new MethodFactory(),
            new Factory\Property(new PrettyPrinter()),
            new Factory\Trait_(),
                ]
        );
    }

    public function create($name, array $files): Project
    {
        return $this->standardProjectFactory->create($name, $files);
    }

}
