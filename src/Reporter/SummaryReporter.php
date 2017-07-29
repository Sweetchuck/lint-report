<?php

namespace Sweetchuck\LintReport\Reporter;

use Sweetchuck\LintReport\ReportWrapperInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

class SummaryReporter extends BaseReporter
{
    /**
     * {@inheritdoc}
     */
    protected function doIt()
    {
        $reportWrapper = $this->getReportWrapper();
        if ($reportWrapper->highestSeverity() === ReportWrapperInterface::SEVERITY_OK) {
            return $this;
        }

        $i = 0;
        foreach ($reportWrapper->yieldFiles() as $fileWrapper) {
            $highestSeverity = $fileWrapper->highestSeverity();
            if ($highestSeverity === ReportWrapperInterface::SEVERITY_OK) {
                $i++;

                continue;
            }

            $this->destinationOutput->writeln($this->highlightHeaderBySeverity(
                $highestSeverity,
                $this->normalizeFilePath($fileWrapper->filePath())
            ));

            $table = new Table($this->destinationOutput);
            $table->setHeaders([
                'Source',
                'Occurrences',
            ]);
            $tableStyleAlignRight = new TableStyle();
            $tableStyleAlignRight->setPadType(STR_PAD_LEFT);
            $table->setColumnStyle(1, $tableStyleAlignRight);
            foreach ($fileWrapper->stats()['source'] as $sourceName => $source) {
                $table->addRow([
                    $this->highlightNormalBySeverity($source['severity'], $sourceName),
                    $source['count'],
                ]);
            }

            $table->render();

            if ($i !== $reportWrapper->countFiles() - 1) {
                $this->destinationOutput->writeln('');
            }

            $i++;
        }

        return $this;
    }
}
