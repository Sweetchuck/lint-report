<?php

namespace Sweetchuck\LintReport;

interface ReporterInterface
{
    public function getReportWrapper(): ReportWrapperInterface;

    /**
     * @return $this
     */
    public function setReportWrapper(ReportWrapperInterface $reportWrapper);

    /**
     * @return string|\Symfony\Component\Console\Output\OutputInterface
     */
    public function getDestination();

    /**
     * @param string|\Symfony\Component\Console\Output\OutputInterface $destination
     *
     * @return $this
     */
    public function setDestination($destination);

    public function getDestinationMode(): string;

    /**
     * @return $this
     */
    public function setDestinationMode(string $destinationMode);

    public function getBasePath(): string;

    /**
     * @return $this
     */
    public function setBasePath(string $basePath);

    public function getFilePathStyle(): ?string;

    /**
     * Allowed values are: "relative", "absolute", null.
     *
     * @return $this
     */
    public function setFilePathStyle(?string $value);

    /**
     * @return $this
     */
    public function generate();
}
