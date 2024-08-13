<?php

namespace Hybridly\Tables\Concerns;

use Hybridly\Tables\Actions\BaseAction;
use Hybridly\Tables\Actions\BulkAction;
use Hybridly\Tables\Actions\InlineAction;
use Hybridly\Tables\Table;
use Illuminate\Support\Collection;

trait HasActions
{
    protected mixed $cachedActions = null;
    protected static ?\Closure $endpointParametersResolver = null;

    public static function resolveEndpointParametersUsing(\Closure $callback): void
    {
        static::$endpointParametersResolver = $callback;
    }

    public function getActions(bool $showHidden = false): Collection
    {
        return $this->cachedActions ??= collect($this->defineActions())
            ->when(!$showHidden)
            ->filter(static fn (BaseAction $action): bool => !$action->isHidden());
    }

    public function getInlineActions(bool $showHidden = false): Collection
    {
        return $this->getActions($showHidden)->filter(static fn (BaseAction $action): bool => $action instanceof InlineAction);
    }

    public function getBulkActions(bool $showHidden = false): Collection
    {
        return $this->getActions($showHidden)->filter(static fn (BaseAction $action): bool => $action instanceof BulkAction);
    }

    protected function defineActions(): array
    {
        return [];
    }

    private function resolveEndpointParameters(): array
    {
        static::$endpointParametersResolver ??= fn () => [];

        return $this->evaluate(
            value: static::$endpointParametersResolver,
            named: [
                'table' => $this,
            ],
            typed: [
                Table::class => $this,
            ],
        );
    }
}
