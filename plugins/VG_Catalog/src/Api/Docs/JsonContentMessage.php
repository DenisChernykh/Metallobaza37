<?php

declare(strict_types=1);

namespace VG_Catalog\Api\Docs;

use Attribute;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class JsonContentMessage extends JsonContent
{
    public function __construct(
        ?string $title = null,
        ?string $action = null,
        bool $success = true,
        string|bool|null $message = null,
        ?string $type = 'string',
        ?string $resourceKey = null,
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
        if ($message) {
            if ($resourceKey) {
                $properties[] = new Property(
                    property: 'data',
                    type: 'object',
                    description: 'Message',
                    properties: [
                        new Property(
                            property: $resourceKey,
                            type: $type,
                            description: 'Message',
                            example: $message
                        )
                    ]
                );
            }
            else {
                $properties[] = new Property(
                    property: 'data',
                    type: $type,
                    description: 'Message',
                    example: $message
                );
            }
        }
        parent::__construct(
            title: $title,
            properties: $properties
        );
    }
}
