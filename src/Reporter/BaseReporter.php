<?php

namespace Sweetchuck\LintReport\Reporter;

use Sweetchuck\LintReport\ReporterInterface;
use Sweetchuck\LintReport\ReportWrapperInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Filesystem\Filesystem;

abstract class BaseReporter implements ReporterInterface
{

    /**
     * @param \League\Container\ContainerInterface $container
     */
    public static function lintReportConfigureContainer($container)
    {
        $container->share('lintCheckstyleReporter', CheckstyleReporter::class);
        $container->share('lintSummaryReporter', SummaryReporter::class);
        $container->share('lintVerboseReporter', VerboseReporter::class);
    }

    /**
     * @var ReportWrapperInterface
     */
    protected $reportWrapper = null;

    /**
     * Output destination.
     *
     * @var string|\Symfony\Component\Console\Output\OutputInterface
     */
    protected $destination = null;

    /**
     * Output destination mode.
     *
     * @var string
     */
    protected $destinationMode = 'w';

    /**
     * Output destination.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $destinationOutput = null;

    /**
     * File handler.
     *
     * @var resource
     */
    protected $destinationResource = null;

    /**
     * @var string
     */
    protected $basePath = '';

    /**
     * @var string|null
     */
    protected $filePathStyle = null;

    /**
     * ReportBase constructor.
     */
    public function __construct()
    {
        $this->setBasePath(getcwd());
    }

    /**
     * {@inheritdoc}
     */
    public function getReportWrapper(): ReportWrapperInterface
    {
        return $this->reportWrapper;
    }

    /**
     * {@inheritdoc}
     */
    public function setReportWrapper(ReportWrapperInterface $reportWrapper)
    {
        $this->reportWrapper = $reportWrapper;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * {@inheritdoc}
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDestinationMode(): string
    {
        return $this->destinationMode;
    }

    /**
     * {@inheritdoc}
     */
    public function setDestinationMode(string $destinationMode)
    {
        $this->destinationMode = $destinationMode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * {@inheritdoc}
     */
    public function setBasePath(string $basePath)
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilePathStyle(): ?string
    {
        return $this->filePathStyle;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilePathStyle(?string $value)
    {
        if (!in_array($value, ['relative', 'absolute', null])) {
            throw new \InvalidArgumentException();
        }

        $this->filePathStyle = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return $this
            ->initDestination()
            ->doIt()
            ->closeDestination();
    }

    /**
     * Initialize the output destination based on the Jar values.
     *
     * @return $this
     */
    protected function initDestination()
    {
        $destination = $this->getDestination();
        $destinationMode = $this->getDestinationMode();
        if (is_string($destination)) {
            $fs = new Filesystem();
            $fs->mkdir(dirname($destination));

            $this->destinationResource = fopen($destination, $destinationMode);
            $this->destinationOutput = new StreamOutput(
                $this->destinationResource,
                OutputInterface::VERBOSITY_NORMAL,
                false
            );
        } else {
            $this->destinationOutput = $destination;
        }

        return $this;
    }

    /**
     * Close the destination resource if it was opened here.
     *
     * @return $this
     */
    protected function closeDestination()
    {
        if ($this->destinationResource) {
            fclose($this->destinationResource);
        }

        return $this;
    }

    /**
     * Convert the source report.
     *
     * @return $this
     */
    abstract protected function doIt();

    protected function normalizeFilePath(string $filePath): string
    {
        $file_path_style = $this->getFilePathStyle();
        if ($file_path_style === null) {
            return $filePath;
        }

        $ds = DIRECTORY_SEPARATOR;
        $isAbsolute = $this->isAbsoluteFilePath($filePath);
        if ($isAbsolute && $file_path_style === 'relative') {
            return preg_replace('@^' . preg_quote($this->getBasePath() . $ds, '@') . '@', '', $filePath);
        } elseif (!$isAbsolute && $file_path_style === 'absolute') {
            return $this->getBasePath() . $ds . $filePath;
        }

        return $filePath;
    }

    protected function isAbsoluteFilePath(string $filePath): bool
    {
        // @todo Use Webmozart.
        $isWin = DIRECTORY_SEPARATOR === '\\';


        return $isWin ? preg_match('@^[a-zA-z]:@', $filePath) : strpos($filePath, '/') === 0;
    }

    /**
     * Set colors.
     *
     * @param string $severity
     *   Severity identifier.
     * @param string $text
     *   Text to decorate.
     *
     * @return string
     *   Decorated text.
     */
    protected function highlightHeaderBySeverity(string $severity, string $text): string
    {
        $patterns = [
            'warning' => '<fg=yellow;options=bold>%s</fg=yellow;options=bold>',
            'error' => '<fg=red;options=bold>%s</fg=red;options=bold>',
        ];

        $pattern = isset($patterns[$severity]) ? $patterns[$severity] : '<info>%s</info>';

        return sprintf($pattern, $text);
    }

    /**
     * Set colors.
     *
     * @param string $severity
     *   Severity identifier.
     * @param string $text
     *   Text to decorate.
     *
     * @return string
     *   Decorated text.
     */
    protected function highlightNormalBySeverity(string $severity, string $text): string
    {
        $patterns = [
            'warning' => '<fg=yellow>%s</fg=yellow>',
            'error' => '<fg=red>%s</fg=red>',
        ];

        $pattern = isset($patterns[$severity]) ? $patterns[$severity] : '<info>%s</info>';

        return sprintf($pattern, $text);
    }
}
