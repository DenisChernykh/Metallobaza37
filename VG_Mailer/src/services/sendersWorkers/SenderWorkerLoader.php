<?php

namespace VG_Mailer\src\services\sendersWorkers;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use VG_Mailer\src\Core;
use VG_Mailer\src\interfaces\SenderWorkerInterface;
use VG_Mailer\src\interfaces\SenderWorkerModelInterface;

class SenderWorkerLoader
{
    private static SenderWorkerLoader $model;

    /**
     * @param bool $onlyUse
     * @return SenderWorkerInterface[]
     * @throws Exception
     */
    public function loadWorkers(bool $onlyUse = false): array
    {
        $result = [];

        $core = Core::make();
        $modulePath = $core->getModulePath(true);
        $sendersWorkersDir = $modulePath . '/src/services/sendersWorkers';
        $modelsDir = $modulePath . '/src/models';

        if (!is_dir($sendersWorkersDir)) {
            throw new Exception('Что-то не так с путями');
        }
        $sendersWorkers = array_diff(scandir($sendersWorkersDir), ['.', '..']);

        foreach ($sendersWorkers as $senderWorker) {
            if (pathinfo($senderWorker, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $senderPath = $sendersWorkersDir . DIRECTORY_SEPARATOR . $senderWorker;
            $senderNamespace = $this->getNamespaceFromFile($senderPath);
            if (!$senderNamespace) {
                continue;
            }

            $senderClass = $senderNamespace . '\\' . pathinfo($senderPath, PATHINFO_FILENAME);
            $senderBasename = pathinfo($senderPath, PATHINFO_FILENAME);
            if (!$this->isSenderWorker($senderClass)) {
                continue;
            }


            $modelFile = $modelsDir . DIRECTORY_SEPARATOR . $senderBasename . 'Model.php';
            if (!file_exists($modelFile)) {
                continue;
            }

            $modelNamespace = $this->getNamespaceFromFile($modelFile);
            $modelClass = $modelNamespace . '\\' . $senderBasename . 'Model';
            if (!$modelNamespace || !class_exists($modelClass)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($modelClass);
            $isAbstract = $reflectionClass->isAbstract();
            $isValidInterface = $reflectionClass->implementsInterface(SenderWorkerModelInterface::class);
            $staticMethodMakeExist = method_exists($modelClass, 'make') && (new ReflectionMethod(
                    $modelClass, 'make'
                ))->isStatic();

            if ($isAbstract || !$isValidInterface || !$staticMethodMakeExist) {
                continue;
            }

            if ($onlyUse) {
                /** @var SenderWorkerModelInterface $modelInstance */
                $modelInstance = $modelClass::make();
                if (!$modelInstance->isUse()) {
                    continue;
                }
            }

            $result[] = $senderClass;
        }

        return $result;
    }

    public static function make(): SenderWorkerLoader
    {
        if (!isset(self::$model)) {
            self::$model = new SenderWorkerLoader();
        }
        return self::$model;
    }

    private function getNamespaceFromFile(string $filePath): ?string
    {
        $lines = file($filePath);
        foreach ($lines as $line) {
            if (preg_match('/^namespace\s+(.+);$/', trim($line), $matches)) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    public function isSenderWorker(string $class): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $reflectionClass = new ReflectionClass($class);
        $isAbstract = $reflectionClass->isAbstract();
        $isValidInterface = $reflectionClass->implementsInterface(SenderWorkerInterface::class);

        return $isValidInterface && !$isAbstract;
    }
}