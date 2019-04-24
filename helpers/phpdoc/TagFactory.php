<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2019 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidoc\helpers\phpdoc;

/**
 * Description of TagFactory
 *
 * @author Andreas Prucha (Abexto - Helicon Software Development) <andreas.prucha@gmail.com>
 */
class TagFactory implements \phpDocumentor\Reflection\DocBlock\TagFactory
{
    /**
     * @var \phpDocumentor\Reflection\DocBlock\StandardTagFactory
     */
    private $standardTagFactory = null;
    
    /**
     * @see \phpDocumentor\Reflection\DocBlock\StandardTagFactory::__construct();
     */
    public function __construct(\phpDocumentor\Reflection\FqsenResolver $fqsenResolver, ?array $tagHandlers = null)
    {
        $this->standardTagFactory = new \phpDocumentor\Reflection\DocBlock\StandardTagFactory($fqsenResolver, $tagHandlers);
    }
    
    public function addParameter($name, $value): void
    {
        $this->standardTagFactory->addParameter($name, $value);
    }

    public function addService($service): void
    {
        $this->standardTagFactory->addService($service);
    }

    public function create($tagLine, \phpDocumentor\Reflection\Types\Context $context = null): \phpDocumentor\Reflection\DocBlock\Tag
    {
        // phpdocumentor does not like a [ at the beginning of the description
        // thus it fails at markdown. If tag body starts with '[' 
        // we do some preprocessing in order to work-around this problem
        // For now we just prefix the body with some useless html stuff
        list($tag, $body) = $this->extractTagParts($tagLine);
        if ($body !== '' && $body[0] === '[') {
            $tagLine = '@'.$tag.' <span></span>'.$body;
        }
        // continue with standard.
        return $this->standardTagFactory->create($tagLine, $context);
    }
    
    /**
     * Extracts all components for a tag.
     *
     * @param string $tagLine
     *
     * @return string[]
     */
    private function extractTagParts($tagLine)
    {
        $matches = [];
        if (! preg_match('/^@(' . \phpDocumentor\Reflection\DocBlock\StandardTagFactory::REGEX_TAGNAME . ')(?:\s*([^\s].*)|$)/us', $tagLine, $matches)) {
            throw new \InvalidArgumentException(
                'The tag "' . $tagLine . '" does not seem to be wellformed, please check it for errors'
            );
        }

        if (count($matches) < 3) {
            $matches[] = '';
        }

        return array_slice($matches, 1);
    }
    

    public function registerTagHandler($tagName, $handler): void
    {
        $this->standardTagFactory->registerTagHandler($tagName, $handler);
    }

}
