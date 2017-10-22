<?php

namespace Sweetchuck\LintReport\Tests\Unit\Reporter;

use Sweetchuck\LintReport\Reporter\SummaryReporter;

class SummaryReporterTest extends BaseReporterTestBase
{
    /**
     * {@inheritdoc}
     */
    protected $reporterName = 'summary';

    /**
     * {@inheritdoc}
     */
    protected $reporterClass = SummaryReporter::class;

    /**
     * {@inheritdoc}
     */
    protected $reporterOutputExtension = 'txt';
}
