<?php

namespace Spatie\Health\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use Spatie\Health\Enums\Status;
use Spatie\Health\ResultStores\StoredCheckResults\StoredCheckResult;

class StatusIndicator extends Component
{
    public function __construct(public StoredCheckResult $result) {}

    #[\Override]
    public function render(): View
    {
        return view('health::status-indicator', [
            'result' => $this->result,
            'backgroundColor' => fn (string $status): string => $this->getBackgroundColor($status),
            'iconColor' => fn (string $status): string => $this->getIconColor($status),
            'icon' => fn (string $status): string => $this->getIcon($status),
        ]);
    }

    protected function getBackgroundColor(string $status): string
    {
        return match ($status) {
            \Spatie\Health\Enums\Status::OK->value => 'md:bg-emerald-100 md:dark:bg-emerald-800',
            \Spatie\Health\Enums\Status::WARNING->value => 'md:bg-yellow-100  md:dark:bg-yellow-800',
            \Spatie\Health\Enums\Status::SKIPPED->value => 'md:bg-blue-100  md:dark:bg-blue-800',
            \Spatie\Health\Enums\Status::FAILED->value, \Spatie\Health\Enums\Status::CRASHED->value => 'md:bg-red-100  md:dark:bg-red-800',
            default => 'md:bg-gray-100 md:dark:bg-gray-600'
        };
    }

    protected function getIconColor(string $status): string
    {
        return match ($status) {
            \Spatie\Health\Enums\Status::OK->value => 'text-emerald-500',
            \Spatie\Health\Enums\Status::WARNING->value => 'text-yellow-500',
            \Spatie\Health\Enums\Status::SKIPPED->value => 'text-blue-500',
            \Spatie\Health\Enums\Status::FAILED->value, \Spatie\Health\Enums\Status::CRASHED->value => 'text-red-500',
            default => 'text-gray-500'
        };
    }

    protected function getIcon(string $status): string
    {
        return match ($status) {
            \Spatie\Health\Enums\Status::OK->value => 'check-circle',
            \Spatie\Health\Enums\Status::WARNING->value => 'exclamation-circle',
            \Spatie\Health\Enums\Status::SKIPPED->value => 'arrow-circle-right',
            \Spatie\Health\Enums\Status::FAILED->value, \Spatie\Health\Enums\Status::CRASHED->value => 'x-circle',
            default => ''
        };
    }
}
