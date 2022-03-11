<?php

declare(strict_types=1);

namespace ImageOptimizer;

/**
 * Handles copying of files if output filepath is diff to input before optimizing
 */
class ChangedOutputOptimizer implements WrapperOptimizer
{
    private string $outputPattern;
    private CommandOptimizer|ChainOptimizer|SmartOptimizer $optimizer;

    public function __construct(string $outputPattern, CommandOptimizer|ChainOptimizer|SmartOptimizer $optimizer)
    {
        $this->outputPattern = $outputPattern;
        $this->optimizer = $optimizer;
    }

    public function optimize(string $filepath): void
    {
        $fileInfo = pathinfo($filepath);
        $outputFilepath = str_replace(
            ['%basename%', '%filename%', '%ext%'],
            [
                $fileInfo['dirname'],
                $fileInfo['filename'],
                isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : ''
            ],
            $this->outputPattern
        );

        $outputChanged = $outputFilepath !== $filepath;

        if ($outputChanged) {
            copy($filepath, $outputFilepath);
            $filepath = $outputFilepath;
        }

        try {
            $this->optimizer->optimize($filepath);
        } catch (\Throwable $exception) {
            if ($outputChanged) {
                unlink($filepath);
            }

            throw $exception;
        }
    }

    public function unwrap(): CommandOptimizer|ChainOptimizer|SmartOptimizer
    {
        return $this->optimizer instanceof WrapperOptimizer ? $this->optimizer->unwrap() : $this->optimizer;
    }
}
