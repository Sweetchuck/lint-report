<?php

namespace Sweetchuck\LintReport;

interface FailureWrapperInterface
{
    public function __construct(array $failure);

    public function severity(): string;

    public function source(): string;

    public function line(): int;

    public function column(): int;

    public function message(): string;
}
