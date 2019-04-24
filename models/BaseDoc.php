<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\apidoc\models;

use yii\base\BaseObject;
use yii\helpers\StringHelper;
use phpDocumentor\Reflection\DocBlock\Tags\Since as SinceTag;
use phpDocumentor\Reflection\DocBlock\Tags\Deprecated as DeprecatedTag;

/**
 * Base class for API documentation information.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 * 
 * @property-read string $name Name of the element
 */
class BaseDoc extends BaseObject implements DocInterface
{
    /**
     * @var \phpDocumentor\Reflection\Types\Context
     */
    public $phpDocContext;
    /**
     * @var \phpDocumentor\Reflection\Fqsen
     */
    private $_fqsen;
    public $sourceFile;
    public $startLine;
    public $endLine;
    public $shortDescription;
    public $description;
    public $since;
    public $deprecatedSince;
    public $deprecatedReason;
    /**
     * @var Tag[]
     */
    public $tags = [];
    
    /**
     * @inheritDoc
     */
    public static function normalizeKey($name)
    {
        return ltrim(($name instanceof \phpDocumentor\Reflection\Fqsen) ? (string)$name : $name, '\\');        
    }
    /**
     * @inheritDoc
     */
    public static function normalizeName($name)
    {
        return static::normalizeKey($name);
    }
    
    /**
     * Sets the FQSEN 
     * @param type $fqsen
     */
    public function setFqsen(\phpDocumentor\Reflection\Fqsen $fqsen)
    {
        $this->_fqsen = $fqsen;
    }

    /**
     * Returns the FQSEN
     * @return \phpDocumentor\Reflection\Fqsen
     */
    public function getFqsen(): \phpDocumentor\Reflection\Fqsen
    {
        return $this->_fqsen;
    }
    
    /**
     * @inheritDoc
     */
    public function getName() {
        return static::normalizeName($this->_fqsen);
    }
    
    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return static::normalizeKey($this->_fqsen);
    }
    

    /**
     * Checks if doc has tag of a given name
     * @param string $name tag name
     * @return bool if doc has tag of a given name
     */
    public function hasTag($name)
    {
        foreach ($this->tags as $tag) {
            if (strtolower($tag->getName()) == $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * Removes tag of a given name
     * @param string $name
     */
    public function removeTag($name)
    {
        foreach ($this->tags as $i => $tag) {
            if (strtolower($tag->getName()) == $name) {
                unset($this->tags[$i]);
            }
        }
    }

    /**
     * Get the first tag of a given name
     * @param string $name tag name.
     * @return Tag|null tag instance, `null` if not found.
     * @since 2.0.5
     */
    public function getFirstTag($name)
    {
        foreach ($this->tags as $i => $tag) {
            if (strtolower($tag->getName()) == $name) {
                return $this->tags[$i];
            }
        }

        return null;
    }

    /**
     * @param 
     * @param Context $context
     * @param array $config
     */
    public function __construct($reflector, $context = null, $config = [])
    {
        parent::__construct($config);

        if ($reflector === null) {
            return;
        }

        // base properties
        $this->setFqsen($reflector->getFqsen());
        
        // reflector 4.0 seems not to deliver start and end line anymore, but a location
        // Currently it's not part of the interface declaration, thus we just check if
        // method getLocation() exists
        if (method_exists($reflector, 'getLocation')) {
            $this->startLine = $reflector->getLocation()->getLineNumber();
            $this->endLine = $this->startLine + 1; // Reflector seems not to provide a end line anymore - assume 1 line for now
        } else {
            $this->startLine = null;
            $this->endLine = null;
        }

        $docblock = $reflector->getDocBlock();
        if ($docblock instanceof \phpDocumentor\Reflection\DocBlock) {
            $this->shortDescription = static::mbUcFirst($docblock->getSummary()); // Previously $docblock->getShortDescription()
            if (empty($this->shortDescription) && !($this instanceof PropertyDoc) && $context !== null && $docblock->getTagsByName('inheritdoc') === null) {
                $context->warnings[] = [
                    'line' => $this->startLine,
                    'file' => $this->sourceFile,
                    'message' => "No short description for " . substr(StringHelper::basename(get_class($this)), 0, -3) . " '{$this->name}'",
                ];
            }
            $this->description = $docblock->getDescription()->render(null); // Previously getLongDescription()->getContents();

            $this->phpDocContext = $docblock->getContext();

            $this->tags = $docblock->getTags();
            foreach ($this->tags as $i => $tag) {
                if ($tag instanceof SinceTag) {
                    $this->since = $tag->getVersion();
                    unset($this->tags[$i]);
                } elseif ($tag instanceof DeprecatedTag) {
                    $this->deprecatedSince = $tag->getVersion();
                    $this->deprecatedReason = $tag->getDescription();
                    unset($this->tags[$i]);
                }
            }

            if ($this->shortDescription === '{@inheritdoc}') {
                // Mock up parsing of '{@inheritdoc}' (in brackets) tag, which is not yet supported at "phpdocumentor/reflection-docblock" 2.x
                // todo consider removal in case of "phpdocumentor/reflection-docblock" upgrade
                /* @todo take care of inheritance */
                $this->shortDescription = '';
            }

        } elseif ($context !== null) {
            $context->warnings[] = [
                'line' => $this->startLine,
                'file' => $this->sourceFile,
                'message' => "No docblock for element '{$this->name}'",
            ];
        }
    }

    // TODO implement
//	public function loadSource($reflection)
//	{
//		$this->sourceFile;
//		$this->startLine;
//		$this->endLine;
//	}
//
//	public function getSourceCode()
//	{
//		$lines = file(YII2_PATH . $this->sourcePath);
//		return implode("", array_slice($lines, $this->startLine - 1, $this->endLine - $this->startLine + 1));
//	}

    /**
     * Extracts first sentence out of text
     * @param string $text
     * @return string
     */
    public static function extractFirstSentence($text)
    {
        if (mb_strlen($text, 'utf-8') > 4 && ($pos = mb_strpos($text, '.', 4, 'utf-8')) !== false) {
            $sentence = mb_substr($text, 0, $pos + 1, 'utf-8');
            if (mb_strlen($text, 'utf-8') >= $pos + 3) {
                $abbrev = mb_substr($text, $pos - 1, 4, 'utf-8');
                if ($abbrev === 'e.g.' || $abbrev === 'i.e.') { // do not break sentence after abbreviation
                    $sentence .= static::extractFirstSentence(mb_substr($text, $pos + 1, mb_strlen($text, 'utf-8'), 'utf-8'));
                }
            }
            return $sentence;
        }

        return $text;
    }

    /**
     * Multibyte version of ucfirst()
     * @since 2.0.6
     */
    protected static function mbUcFirst($string)
    {
        $firstChar = mb_strtoupper(mb_substr($string, 0, 1, 'utf-8'), 'utf-8');
        return $firstChar . mb_substr($string, 1, mb_strlen($string, 'utf-8'), 'utf-8');
    }


}
