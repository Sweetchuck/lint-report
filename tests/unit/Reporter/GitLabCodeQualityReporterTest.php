<?php

declare(strict_types = 1);

namespace Sweetchuck\LintReport\Tests\Unit\Reporter;

use Sweetchuck\LintReport\Reporter\GitLabCodeQualityReporter;

class GitLabCodeQualityReporterTest extends BaseReporterTestBase
{

    protected string $reporterName = 'gitlabCodeQuality';

    protected string $reporterClass = GitLabCodeQualityReporter::class;

    protected string $reporterOutputExtension = 'json';

    protected string $expectedEmptyOutput = "[]\n";

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
