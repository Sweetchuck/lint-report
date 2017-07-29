<?php

namespace Sweetchuck\LintReport\Reporter;

use Sweetchuck\LintReport\ReportWrapperInterface;

class CheckstyleReporter extends BaseReporter
{
    /**
     * {@inheritdoc}
     */
    protected function doIt()
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = true;
        $e_checkstyle = $dom->createElement('checkstyle');
        $e_checkstyle->setAttribute('version', '2.6.1');
        $dom->appendChild($e_checkstyle);

        $reportWrapper = $this->getReportWrapper();
        foreach ($reportWrapper->yieldFiles() as $fileWrapper) {
            if ($fileWrapper->highestSeverity() === ReportWrapperInterface::SEVERITY_OK) {
                continue;
            }

            $e_file = $dom->createElement('file');
            $e_file->setAttribute('name', $this->normalizeFilePath($fileWrapper->filePath()));
            $e_checkstyle->appendChild($e_file);

            foreach ($fileWrapper->yieldFailures() as $failureWrapper) {
                $e_error = $dom->createElement('error');
                $e_file->appendChild($e_error);
                $e_error->setAttribute('severity', $failureWrapper->severity());
                $e_error->setAttribute('source', $failureWrapper->source());
                $e_error->setAttribute('line', $failureWrapper->line());
                $e_error->setAttribute('column', $failureWrapper->column());
                $e_error->setAttribute('message', $failureWrapper->message());
            }
        }
        $this->destinationOutput->write($dom->saveXML());

        return $this;
    }
}
