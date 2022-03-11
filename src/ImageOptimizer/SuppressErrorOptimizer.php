<?php

declare(strict_types=1);

namespace ImageOptimizer;

use ImageOptimizer\Exception\Exception;
use Psr\Log\LoggerInterface;

/**
 * Logs optimization error output rather than throwing an exception
 */
class SuppressErrorOptimizer implements WrapperOptimizer
{
    private ChangedOutputOptimizer $optimizer;
    private LoggerInterface $logger;

    public function __construct(ChangedOutputOptimizer $optimizer, LoggerInterface $logger)
    {
        $this->optimizer = $optimizer;
        $this->logger = $logger;
    }

    public function optimize(string $filepath): void
    {
        try {
            $this->optimizer->optimize($filepath);
        } catch (Exception $e) {
            $this->logger->error(
                'Error during image optimization. See exception for more details.',
                ['exception' => $e]
            );
        }
    }

    public function unwrap(): CommandOptimizer|ChainOptimizer|SmartOptimizer
    {
        return $this->optimizer->unwrap();
    }
}
