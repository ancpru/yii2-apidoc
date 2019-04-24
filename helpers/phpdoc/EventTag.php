<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2019 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */


namespace yii\apidoc\helpers\phpdoc;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use Webmozart\Assert\Assert;

/**
 * Reflection class for Yii-specific {@}event tag in a Docblock.
 */
final class EventTag extends BaseTag implements StaticMethod
{
    protected $name = 'event';

    /** @var Type */
    private $type;

    public function __construct(Type $type, ?Description $description = null)
    {
        $this->type = $type;
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(
        string $body,
        ?TypeResolver $typeResolver = null,
        ?DescriptionFactory $descriptionFactory = null,
        ?TypeContext $context = null
    ): self {
        Assert::allNotNull([$typeResolver, $descriptionFactory]);

        $parts = preg_split('/\s+/Su', $body, 2);

        $type = $typeResolver->resolve($parts[0] ?? '', $context);
        $description = $descriptionFactory->create($parts[1] ?? '', $context);

        return new static($type, $description);
    }

    /**
     * Returns the type section of the variable.
     */
    public function getType(): Type
    {
        return $this->type;
    }

    public function __toString(): string
    {
        return $this->type . ' ' . $this->description;
    }
}
