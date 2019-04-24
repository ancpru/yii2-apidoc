<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2019 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidoc\helpers\phpdoc;

/**
 * Description of DocBlockFactory
 *
 * @author Andreas Prucha (Abexto - Helicon Software Development) <andreas.prucha@gmail.com>
 */
class DocBlockFactoryFactory
{    

    public static function createInstance(array $additionalTags = []): \phpDocumentor\Reflection\DocBlockFactoryInterface
    {
        $fqsenResolver = new \phpDocumentor\Reflection\FqsenResolver();
        $tagFactory = new TagFactory($fqsenResolver, $additionalTags);
        $descriptionFactory = new \phpDocumentor\Reflection\DocBlock\DescriptionFactory($tagFactory);

        $tagFactory->addService($descriptionFactory);
        $tagFactory->addService(new \phpDocumentor\Reflection\TypeResolver($fqsenResolver));

        $docBlockFactory = new DocBlockFactory($descriptionFactory, $tagFactory);

        return $docBlockFactory;
    }
    

}
