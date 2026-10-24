<?php

declare(strict_types=1);

namespace VG_Catalog\Api\Docs;

use Attribute;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class JsonContentWithResourceKey extends JsonContent
{
    public function __construct(
        ?string $title = null,
        object|string|null $ref = null,
        ?string $resourceKey = null,
        ?bool $dataIsArray = false,
        ?string $description = null,
        ?string $action = null,
        bool $success = true,
    ) {
        $properties = [
            new Property(
                property: 'success',
                example: $success,
                type: 'bool',
            ),
            new Property(
                property: 'action',
                example: $action,
                type: 'object',
            ),
        ];
        if ($dataIsArray) {
            $properties[] = new Property(
                property: 'data',
                type: 'object',
                properties: [
                    new Property(
                        property: $resourceKey,
                        description: $description,
                        type: 'array',
                        items: new Items(ref: $ref),
                    ),
                ]
            );
        }
        else {
            if ($resourceKey) {
                $properties[] = new Property(
                    property: 'data',
                    type: 'object',
                    properties: [
                        new Property(
                            property: $resourceKey,
                            description: $description,
                            type: 'object',
                            ref: $ref
                        )
                    ]
                );
            }
            else {
                $properties[] = new Property(
                    property: 'data',
                    type: 'object',
                    ref: $ref
                );
            }
        }
        if ($resourceKey) {
            parent::__construct(
                title: $title,
                required: [$resourceKey],
                properties: $properties
            );
        }
        else {
            parent::__construct(
                title: $title,
                properties: $properties
            );
        }

    }
}
