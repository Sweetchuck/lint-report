<?php

declare(strict_types = 1);

namespace Sweetchuck\LintReport\Tests\Unit\Reporter;

use Sweetchuck\LintReport\Reporter\CheckstyleReporter;

class CheckstyleReporterTest extends BaseReporterTestBase
{

    protected string $reporterName = 'checkstyle';

    protected string $reporterClass = CheckstyleReporter::class;

    protected string $reporterOutputExtension = 'xml';

    protected string $expectedEmptyOutput = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<checkstyle version="2.6.1"/>

XML;

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
