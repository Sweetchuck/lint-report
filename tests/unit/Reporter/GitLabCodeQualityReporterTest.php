<?php

namespace Sweetchuck\LintReport\Tests\Unit\Reporter;

use Sweetchuck\LintReport\Reporter\GitLabCodeQualityReporter;

class GitLabCodeQualityReporterTest extends BaseReporterTestBase
{

    /**
     * {@inheritdoc}
     */
    protected $reporterName = 'gitlabCodeQuality';

    /**
     * {@inheritdoc}
     */
    protected $reporterClass = GitLabCodeQualityReporter::class;

    /**
     * {@inheritdoc}
     */
    protected $reporterOutputExtension = 'json';

    protected $expectedEmptyOutput = "[]\n";

    public function testSetFilePathStyle()
    {
        try {
            $this->reporter->setFilePathStyle('invalid');
            $this->fail('Expected exception is missing.');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true, 'Invalid value cannot be applied.');
        }
    }
}
