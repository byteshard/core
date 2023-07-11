<?php
/**
 * @copyright  Copyright (c) 2009 Bespin Studios GmbH
 * @license    See LICENSE file that is distributed with this source code
 */

namespace byteShard\Internal\Chart;

abstract class View
{
    private readonly ChartType $view;
    private readonly ?string   $value;

    public function __construct(ChartType $view, ?string $value)
    {
        $this->view  = $view;
        $this->value = $value;
    }

    /** @return array<string,bool|int|null|string> */
    abstract public function getView(): array;

    /**
     * @param array<string,bool|int|null|string> ...$unfilteredView
     * @return array<string,bool|int|null|string>
     */
    protected function getFilteredView(array ...$unfilteredView): array
    {
        $view = array_merge(
            [
                'view'  => $this->view->value,
                'value' => $this->value ?? null
            ],
            ...$unfilteredView
        );
        return array_filter($view, function ($value) {
            return $value !== null;
        });
    }
}
