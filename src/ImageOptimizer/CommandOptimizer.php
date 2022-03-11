<?php

declare(strict_types=1);

namespace ImageOptimizer;

/**
 * Runs the Command upon optimization
 */
class CommandOptimizer implements Optimizer
{
    public Command $command;
    /**
     * @var null|\Closure|array<string>
     */
    public null|\Closure|array $extraArgs;

    /**
     * @param Command $command
     * @param null|\Closure|array<string>|null $extraArgs
     */
    public function __construct(Command $command, null|\Closure|array $extraArgs = null)
    {
        $this->command = $command;
        $this->extraArgs = $extraArgs;
    }

    public function optimize(string $filepath): void
    {
        $customArgs = [$filepath];

        if ($this->extraArgs) {
            $customArgs = array_merge(
                is_callable($this->extraArgs) ? call_user_func($this->extraArgs, $filepath) : $this->extraArgs,
                $customArgs
            );
        }

        $this->command->execute($customArgs);
    }
}
