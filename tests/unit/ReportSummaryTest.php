<?php

use Sweetchuck\LintReport\Reporter\SummaryReporter;

// @codingStandardsIgnoreStart
class ReportSummaryTest extends ReportTestBase
{
    // @codingStandardsIgnoreEnd

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
