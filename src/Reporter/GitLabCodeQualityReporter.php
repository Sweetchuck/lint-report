<?php

declare(strict_types=1);

namespace Sweetchuck\LintReport\Reporter;

use Sweetchuck\LintReport\FailureWrapperInterface;

class GitLabCodeQualityReporter extends BaseReporter
{
    protected function doIt()
    {
        $this
            ->destinationOutput
            ->writeln($this->jsonEncode($this->convertReport()));

        return $this;
    }

    protected function jsonEncode(array $data): string
    {
        return json_encode($data, $this->getJsonDecodeOptions());
    }

    protected function getJsonDecodeOptions(): int
    {
        return JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
    }

    protected function convertReport(): array
    {
        $report = [];
        foreach ($this->getReportWrapper()->yieldFiles() as $fileWrapper) {
            $filePath = $this->normalizeFilePath($fileWrapper->filePath());
            foreach ($fileWrapper->yieldFailures() as $failureWrapper) {
                $report[] = $this->convertFailure($filePath, $failureWrapper);
            }
        }

        return $report;
    }

    protected function convertFailure(
        string $filePath,
        FailureWrapperInterface $failureWrapper
    ): array {
        return [
            'description' => $failureWrapper->message(),
            'fingerprint' => $failureWrapper->source(),
            'location' => [
                'path' => $filePath,
                'positions' => [
                    'begin' => [
                        'line' => $failureWrapper->line(),
                        'column' => $failureWrapper->column(),
                    ],
                ],
            ],
        ];
    }
}
