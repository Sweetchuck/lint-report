<?php

namespace Sweetchuck\LintReport;

interface ReportWrapperInterface
{
    /**
     * @var string
     */
    const SEVERITY_ERROR = 'error';

    /**
     * @var string
     */
    const SEVERITY_WARNING = 'warning';

    /**
     * @var string
     */
    const SEVERITY_OK = 'ok';

    public function __construct(?array $report = null);

    public function getReport(): array;

    /**
     * @return $this
     */
    public function setReport(array $report);

    public function numOfErrors(): int;

    public function numOfWarnings(): int;

    public function highestSeverity(): string;

    public function countFiles(): int;

    /**
     * @return \Sweetchuck\LintReport\FileWrapperInterface[]
     */
    public function yieldFiles();
}
