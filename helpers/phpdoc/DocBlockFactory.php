<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace yii\apidoc\helpers\phpdoc;

use phpDocumentor\Reflection\DocBlockFactory as StandardDocBlockFactory;

/**
 * Description of DocBlockFactory
 *
 * @author Andreas Prucha, Abexto - Helicon Software Development <andreas.prucha@gmail.com>
 */
class DocBlockFactory implements \phpDocumentor\Reflection\DocBlockFactoryInterface
{
    /**
     * @var StandardDocBlockFactory
     */
    private $standardDocBlockFactory = null;
    
    public function __construct(\phpDocumentor\Reflection\DocBlock\DescriptionFactory $descriptionFactory, \phpDocumentor\Reflection\DocBlock\TagFactory $tagFactory)
    {
        $this->standardDocBlockFactory = new StandardDocBlockFactory($descriptionFactory, $tagFactory);
    }
    
    
    public function create($docblock, ?\phpDocumentor\Reflection\Types\Context $context = null, ?\phpDocumentor\Reflection\Location $location = null): \phpDocumentor\Reflection\DocBlock
    {
        return $this->standardDocBlockFactory->create($docblock, $context, $location);
    }

    public static function createInstance(array $additionalTags = array()): StandardDocBlockFactory
    {
        throw new \Exception ('Do not use '.__METHOD__.'. Use '.DocBlockFactoryFactory::class.'::createInstance() instead');
    }
}
