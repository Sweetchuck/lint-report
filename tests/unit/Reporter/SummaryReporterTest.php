<?php

declare(strict_types = 1);

namespace Sweetchuck\LintReport\Tests\Unit\Reporter;

use Sweetchuck\LintReport\Reporter\SummaryReporter;

class SummaryReporterTest extends BaseReporterTestBase
{
    protected string $reporterName = 'summary';

    protected string $reporterClass = SummaryReporter::class;

    protected string $reporterOutputExtension = 'txt';
}
