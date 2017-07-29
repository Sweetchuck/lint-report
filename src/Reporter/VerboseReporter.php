<?php

namespace Sweetchuck\LintReport\Reporter;

use Sweetchuck\LintReport\ReportWrapperInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

class VerboseReporter extends BaseReporter
{

    protected $columns = [
        'severity' => [
            'header' => 'Severity',
            'visible' => true,
            'padType' => null,
        ],
        'source' => [
            'header' => 'Source',
            'visible' => false,
            'padType' => null,
        ],
        'line' => [
            'header' => 'Line',
            'visible' => true,
            'padType' => STR_PAD_LEFT,
        ],
        'column' => [
            'header' => 'Column',
            'visible' => false,
            'padType' => STR_PAD_LEFT,
        ],
        'message' => [
            'header' => 'Message',
            'visible' => true,
            'padType' => null,
        ],
    ];

    public function isSeverityVisible(): bool
    {
        return $this->columns['severity']['visible'];
    }

    /**
     * @return $this
     */
    public function showSeverity(bool $visible)
    {
        $this->columns['severity']['visible'] = $visible;

        return $this;
    }

    public function isSourceVisible(): bool
    {
        return $this->columns['source']['visible'];
    }

    /**
     * @return $this
     */
    public function showSource(bool $visible)
    {
        $this->columns['source']['visible'] = $visible;

        return $this;
    }

    public function isLineNumberVisible(): bool
    {
        return $this->columns['line']['visible'];
    }

    /**
     * @return $this
     */
    public function showLineNumber(bool $visible)
    {
        $this->$this->columns['line']['visible'] = $visible;

        return $this;
    }

    public function isColumnNumberVisible(): bool
    {
        return $this->columns['column']['visible'];
    }

    /**
     * @return $this
     */
    public function showColumnNumber(bool $visible)
    {
        $this->columns['column']['visible'] = $visible;

        return $this;
    }

    public function isMessageVisible(): bool
    {
        return $this->columns['message']['visible'];
    }

    /**
     * @return $this
     */
    public function showMessage(bool $visible)
    {
        $this->columns['message']['visible'] = $visible;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function doIt()
    {
        $reportWrapper = $this->getReportWrapper();
        if ($reportWrapper->highestSeverity() === ReportWrapperInterface::SEVERITY_OK) {
            return $this;
        }

        $columns = $this->filterHiddenColumns();
        $header = $this->getTableHeader($columns);

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
            $table->setHeaders($header);
            $tableStyleAlignRight = new TableStyle();
            $tableStyleAlignRight->setPadType(STR_PAD_LEFT);
            $c = 0;
            foreach ($columns as $column) {
                if ($column['padType'] === STR_PAD_LEFT) {
                    $table->setColumnStyle($c, $tableStyleAlignRight);
                }
                $c++;
            }
            foreach ($fileWrapper->yieldFailures() as $failureWrapper) {
                $row = [];
                foreach (array_keys($columns) as $columnName) {
                    switch ($columnName) {
                        case 'severity':
                            $row[] = $failureWrapper->severity();
                            break;

                        case 'source':
                            $row[] = $failureWrapper->source();
                            break;

                        case 'line':
                            $row[] = $failureWrapper->line();
                            break;

                        case 'column':
                            $row[] = $failureWrapper->column();
                            break;

                        case 'message':
                            $row[] = $failureWrapper->message();
                            break;
                    }
                }

                $table->addRow($row);
            }

            $table->render();

            if ($i !== $reportWrapper->countFiles() - 1) {
                $this->destinationOutput->writeln('');
            }

            $i++;
        }

        return $this;
    }

    protected function filterHiddenColumns(): array
    {
        $columns = [];
        foreach ($this->columns as $columnName => $column) {
            if ($column['visible']) {
                $columns[$columnName] = $column;
            }
        }

        return $columns;
    }

    /**
     * @return string[]
     */
    protected function getTableHeader(array $columns): array
    {
        $header = [];
        foreach ($columns as $column) {
            $header[] = $column['header'];
        }

        return $header;
    }
}
