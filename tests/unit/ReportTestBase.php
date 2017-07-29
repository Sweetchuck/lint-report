<?php

use Sweetchuck\LintReport\Test\Helper\Dummy\LintReportWrapper\ReportWrapper as DummyReportWrapper;
use Sweetchuck\LintReport\ReportWrapperInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ReportTestBase.
 */
// @codingStandardsIgnoreStart
class ReportTestBase extends \Codeception\Test\Unit
{
    // @codingStandardsIgnoreEnd

    /**
     * @var string
     */
    protected $reporterName = '';

    /**
     * @var string
     */
    protected $reporterClass = '';

    /**
     * @var string
     */
    protected $reporterOutputExtension = '';

    /**
     * @return array
     */
    public function casesGenerate()
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
     *
     * @param ReportWrapperInterface $reportWrapper
     * @param string|null $filePathStyle
     * @param string $expected
     */
    public function testGenerate(
        ReportWrapperInterface $reportWrapper,
        $filePathStyle,
        $expected
    ) {
        $destination = new BufferedOutput();

        /** @var \Sweetchuck\LintReport\ReporterInterface $reporter */
        $reporter = new $this->reporterClass();
        $reporter
            ->setReportWrapper($reportWrapper)
            ->setDestination($destination)
            ->setBasePath('/foo')
            ->setFilePathStyle($filePathStyle)
            ->generate();

        static::assertEquals($expected, $destination->fetch());
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    protected function yamlParse($fileName)
    {
        if (function_exists('yaml_parse_file')) {
            return yaml_parse_file($fileName, -1);
        }

        return $this->yamlParseSymfonyMultiDocument($fileName);
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    protected function yamlParseSymfonyMultiDocument($fileName)
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
