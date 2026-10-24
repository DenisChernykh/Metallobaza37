<?php

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;

$config = [
    'inputPath' => [
        __DIR__ . '/../src/Api',
        __DIR__ . '/../src/Domain',
    ],
    'analysers' => [
        [AttributeAnnotationFactory::class],
        [DocBlockAnnotationFactory::class],
    ],
    'outputPath' => 'openapi.yaml'
];