<?php

namespace Sweetchuck\LintReport;

interface FileWrapperInterface
{
    public function __construct(array $file);

    public function filePath(): string;

    public function numOfErrors(): int;

    public function numOfWarnings(): int;

    public function highestSeverity(): string;

    /**
     * @return \Sweetchuck\LintReport\FailureWrapperInterface[]
     */
    public function yieldFailures();

    public function stats(): array;
}
