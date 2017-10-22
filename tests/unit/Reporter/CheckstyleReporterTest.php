<?php

namespace Sweetchuck\LintReport\Tests\Unit\Reporter;

use Sweetchuck\LintReport\Reporter\CheckstyleReporter;

class CheckstyleReporterTest extends BaseReporterTestBase
{

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

    protected $expectedEmptyOutput = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<checkstyle version=\"2.6.1\"/>\n";

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
