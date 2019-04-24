<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2019 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidoc\helpers\phpdoc;

/**
 * Representation of the {@}see tag
 * 
 * The original implementation of the see-tag is quite strict and requires 
 * well-formed urls or references. As we use markdown we implement a version
 * that basically just provides a description 
 *
 * @author Andreas Prucha (Abexto - Helicon Software Development) <andreas.prucha@gmail.com>
 */
class SeeTag extends \phpDocumentor\Reflection\DocBlock\Tags\Generic
{
}
