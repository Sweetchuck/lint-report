<?php

namespace Sweetchuck\LintReport\Tests\Unit\Reporter;

use Sweetchuck\LintReport\Reporter\VerboseReporter;
use Sweetchuck\LintReport\ReportWrapperInterface;
use Sweetchuck\LintReport\Test\Helper\Dummy\LintReportWrapper\ReportWrapper as DummyReportWrapper;
use Symfony\Component\Console\Output\BufferedOutput;

class VerboseReporterTest extends BaseReporterTestBase
{
    /**
     * {@inheritdoc}
     */
    protected $reporterName = 'verbose';

    /**
     * {@inheritdoc}
     */
    protected $reporterClass = VerboseReporter::class;

    /**
     * @var \Sweetchuck\LintReport\Reporter\VerboseReporter
     */
    protected $reporter;

    /**
     * {@inheritdoc}
     */
    protected $reporterOutputExtension = 'txt';

    public function casesGenerateColumns(): array
    {
        $report = [
            [
                'filePath' => '/a.js',
                'errorCount' => 1,
                'warningCount' => 0,
                'messages' => [
                    [
                        'ruleId' => 'r',
                        'severity' => 2,
                        'message' => 'm',
                        'line' => 3,
                        'column' => 4,
                        'nodeType' => 'n',
                        'fix' => [
                            'range' => [5, 6],
                            'text' => 't',
                        ],
                    ],
                ],
            ],
        ];

        $expected = [];
        $expected['all'] = <<< TEXT
/a.js
+----------+--------+------+--------+---------+
| Severity | Source | Line | Column | Message |
+----------+--------+------+--------+---------+
| error    | r      |    3 |      4 | m       |
+----------+--------+------+--------+---------+

TEXT;
        $expected['showSeverity'] = <<< TEXT
/a.js
+--------+------+--------+---------+
| Source | Line | Column | Message |
+--------+------+--------+---------+
| r      |    3 |      4 | m       |
+--------+------+--------+---------+

TEXT;
        $expected['showSource'] = <<< TEXT
/a.js
+----------+------+--------+---------+
| Severity | Line | Column | Message |
+----------+------+--------+---------+
| error    |    3 |      4 | m       |
+----------+------+--------+---------+

TEXT;
        $expected['showLineNumber'] = <<< TEXT
/a.js
+----------+--------+--------+---------+
| Severity | Source | Column | Message |
+----------+--------+--------+---------+
| error    | r      |      4 | m       |
+----------+--------+--------+---------+

TEXT;
        $expected['showColumnNumber'] = <<< TEXT
/a.js
+----------+--------+------+---------+
| Severity | Source | Line | Message |
+----------+--------+------+---------+
| error    | r      |    3 | m       |
+----------+--------+------+---------+

TEXT;
        $expected['showMessage'] = <<< TEXT
/a.js
+----------+--------+------+--------+
| Severity | Source | Line | Column |
+----------+--------+------+--------+
| error    | r      |    3 |      4 |
+----------+--------+------+--------+

TEXT;

        $optionsTemplate = [
            'showSeverity' => true,
            'showSource' => true,
            'showLineNumber' => true,
            'showColumnNumber' => true,
            'showMessage' => true,
        ];

        $cases = [];
        foreach (array_keys($expected) as $caseId) {
            $options = $optionsTemplate;
            if ($caseId !== 'all') {
                $options[$caseId] = false;
            }

            $cases[$caseId] = [
                $expected[$caseId],
                $this->createDummyReportWrapper($report),
                $options
            ];
        }

        return $cases;
    }

    /**
     * @dataProvider casesGenerateColumns
     */
    public function testGenerateColumns(
        string $expected,
        ReportWrapperInterface $reportWrapper,
        array $options
    ) {
        $destination = new BufferedOutput();

        $this->reporter
            ->setReportWrapper($reportWrapper)
            ->setDestination($destination)
            ->setOptions($options)
            ->generate();

        static::assertEquals($expected, $destination->fetch());
    }

    public function testGetSet()
    {
        foreach ([true, false] as $value) {
            $this->reporter->showMessage($value);
            static::assertEquals($value, $this->reporter->isMessageVisible());

            $this->reporter->showSeverity($value);
            static::assertEquals($value, $this->reporter->isSeverityVisible());

            $this->reporter->showLineNumber($value);
            static::assertEquals($value, $this->reporter->isLineNumberVisible());

            $this->reporter->showColumnNumber($value);
            static::assertEquals($value, $this->reporter->isColumnNumberVisible());

            $this->reporter->showSource($value);
            static::assertEquals($value, $this->reporter->isSourceVisible());
        }
    }
}
