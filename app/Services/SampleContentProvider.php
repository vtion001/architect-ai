<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Report\SampleContentProvider as ReportSampleContentProvider;

/**
 * Backwards-compatible SampleContentProvider alias.
 *
 * Extends the Report\SampleContentProvider to support legacy type-hints.
 */
class SampleContentProvider extends ReportSampleContentProvider
{
}
