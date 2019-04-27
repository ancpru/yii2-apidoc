<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace yii\apidoc\helpers\phpdoc;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;

/**
 * Fixed type resolving
 * 
 * Trait for "bug-fixing" factories
 * 
 * At the moment the standard implementation of the factory class seems to ignore resolve 
 * the result type in a wrong way as it calls resolveName even for already fully qualified names
 * and ends up in having the namespace in the result type twice (\My\Namespace\My\Namespace\Class)
 * 
 * @todo Check when new phpdocumentor/reflection versions are released if this workaround is still
 * necessary or if there are better ways to handle this
 * 
 * @author Andreas Prucha, Abexto - Helicon Software Development <andreas.prucha@gmail.com>
 */
trait ResolveNameNodeFixTrait
{
    
    /**
     * Converts the Parser node into a string
     * @param NullableType|Identifier|Name Node to handle
     * @return string Node as string: `[?][\][Namespace\]Class`
     */
    protected function nodeAsTypeString($node)
    {
        $typeNode = ($node instanceof NullableType) ? $node->type : $node;
        $typeAsString = (string)$typeNode;
        if ($typeNode instanceof Name && $typeNode->isFullyQualified() && !$typeNode->isSpecialClassName() && $typeAsString[0] !== '\\') {
            $typeAsString = '\\'.$typeAsString;
        }
        if ($node instanceof NullableType) {
            $typeAsString = '?'.$typeAsString;
        }
        return $typeAsString;
    }

    /**
     * Returns the resolved type from the node
     * @param Node $node
     * @param  $context
     * @return Type
     */
    protected function resolveNode($node, $context)
    {
        $result = null;
        if (isset($node)) {
            $typeResolver = new TypeResolver();
            $typeString = $this->nodeAsTypeString($node);
            $result = $typeResolver->resolve($typeString, $context);
        }
        return $result;
    }

}
