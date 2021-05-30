<?php

declare(strict_types = 1);

namespace Sweetchuck\LintReport\Tests\Unit\Reporter;

use Codeception\Test\Unit;
use org\bovigo\vfs\vfsStream;
use Sweetchuck\LintReport\ReporterInterface;
use Sweetchuck\LintReport\Test\Helper\Dummy\LintReportWrapper\ReportWrapper as DummyReportWrapper;
use Sweetchuck\LintReport\ReportWrapperInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Yaml\Yaml;

class BaseReporterTestBase extends Unit
{

    protected string $reporterName = '';

    protected string $reporterClass = '';

    protected ReporterInterface $reporter;

    protected string $reporterOutputExtension = '';

    protected string $expectedEmptyOutput = '';

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->reporter = new $this->reporterClass();
    }

    public function casesGenerate(): array
    {
        $cases = [];

        $dataDir = rtrim(codecept_data_dir(), '/');
        $file = new \DirectoryIterator("$dataDir/source");

        $sourceType2WrapperClass = [
            'eslint' => DummyReportWrapper::class,
        ];

        while ($file->valid()) {
            if ($file->isDir()) {
                $file->next();

                continue;
            }

            $baseName = $file->getBasename();
            list($sourceType, $number, $extension) = explode('.', $baseName);
            $baseName = "$sourceType.$number";

            foreach (['relative', 'absolute', null] as $filePathStyle) {
                $filePathStyleStr = ($filePathStyle ?: 'null');
                $expected = implode('.', [
                    $this->reporterName,
                    $number,
                    $filePathStyleStr,
                    $this->reporterOutputExtension,
                ]);

                $caseId = "{$this->reporterName}.$baseName.$filePathStyleStr";

                $wrapperClass = $sourceType2WrapperClass[$sourceType];

                if ($extension === 'json') {
                    $report = json_decode(file_get_contents($file->getPathname()), true);
                } else {
                    $report = $this->yamlParse($file->getPathname());
                }

                $cases[$caseId] = [
                    'reportWrapper' => new $wrapperClass($report),
                    'filePathStyle' => $filePathStyle,
                    'expected' => file_get_contents("$dataDir/expected/$expected"),
                ];
            }

            $file->next();
        }

        return $cases;
    }

    /**
     * @dataProvider casesGenerate
     */
    public function testGenerateOutputDestination(
        ReportWrapperInterface $reportWrapper,
        ?string $filePathStyle,
        string $expected
    ) {
        $destination = new BufferedOutput();

        $this->reporter
            ->setReportWrapper($reportWrapper)
            ->setDestination($destination)
            ->setBasePath('/foo')
            ->setFilePathStyle($filePathStyle)
            ->generate();

        static::assertEquals($expected, $destination->fetch());
    }

    /**
     * @dataProvider casesGenerate
     */
    public function testGenerateFileDestination(
        ReportWrapperInterface $reportWrapper,
        ?string $filePathStyle,
        string $expected
    ) {
        $srcDir = __FUNCTION__;
        $vfs = vfsStream::setup(
            'root',
            0777,
            [
                $srcDir => [],
            ]
        );

        $destination = $vfs->url() . "/$srcDir/" . $this->dataName() . '.txt';

        $this->reporter
            ->setOptions([
                'reportWrapper' => $reportWrapper,
                'destination' => $destination,
                'destinationMode' => 'w',
                'basePath' => '/foo',
                'filePathStyle' => $filePathStyle,
            ])
            ->generate();

        static::assertEquals($expected, file_get_contents($destination));
    }

    public function testGenerateEmpty()
    {
        $report = [
            [
                'filePath' => '/a.js',
                'errorCount' => 0,
                'warningCount' => 0,
                'messages' => [],
            ],
        ];
        $destination = new BufferedOutput();

        $this->reporter
            ->setReportWrapper($this->createDummyReportWrapper($report))
            ->setDestination($destination)
            ->generate();

        static::assertEquals($this->expectedEmptyOutput, $destination->fetch());
    }

    protected function createDummyReportWrapper(array $report): ReportWrapperInterface
    {
        return new DummyReportWrapper($report);
    }

    protected function yamlParse(string $fileName): array
    {
        if (function_exists('yaml_parse_file')) {
            return yaml_parse_file($fileName, -1);
        }

        return $this->yamlParseSymfonyMultiDocument($fileName);
    }

    protected function yamlParseSymfonyMultiDocument(string $fileName): array
    {
        $documents = preg_split(
            '@(^|\n)---\n(?=failures:\n)@',
            file_get_contents($fileName),
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        for ($i = 0; $i < count($documents); $i++) {
            $documents[$i] = Yaml::parse($documents[$i]);
        }

        return $documents;
    }
}
