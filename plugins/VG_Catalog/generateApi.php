<?php
require_once 'vendor/autoload.php';

use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Generator;
use OpenApi\Util;
include __DIR__ . '/config/doc.php';

$analysers = array_map(function($analyser){
    return new $analyser[0](...($analyser[1] ?? []));
}, $config['analysers']);
$analyser = new ReflectionAnalyser($analysers);
$generator = new Generator();

$openapi = $generator
    ->setConfig([])
    ->setAnalyser($analyser)
    ->generate(Util::finder($config['inputPath']));

$openapi->saveAs($config['outputPath'], 'yaml');
echo "openapi.yaml created\n";
