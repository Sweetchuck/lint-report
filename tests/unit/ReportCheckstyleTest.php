<?php

use Sweetchuck\LintReport\Reporter\CheckstyleReporter;

/**
 * Class TaskScssLintRunTest.
 */
// @codingStandardsIgnoreStart
class ReportCheckstyleTest extends ReportTestBase
{
    // @codingStandardsIgnoreEnd

    /**
     * {@inheritdoc}
     */
    protected $reporterName = 'checkstyle';

    /**
     * {@inheritdoc}
     */
    protected $reporterClass = CheckstyleReporter::class;

    /**
     * {@inheritdoc}
     */
    protected $reporterOutputExtension = 'xml';

    public function testSetFilePathStyle()
    {
        /** @var \Sweetchuck\LintReport\ReporterInterface $reporter */
        $reporter = new $this->reporterClass();
        try {
            $reporter->setFilePathStyle('invalid');
            $this->fail('Expected exception is missing.');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, 'Invalid value cannot be applied.');
        }
    }
}
