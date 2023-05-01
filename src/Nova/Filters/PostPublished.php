<?php

namespace Novius\LaravelNovaNews\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class PostPublished extends Filter
{
    const DRAFT = 'Draft';

    const PUBLISHED = 'Published';

    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        if ((string) $value === self::DRAFT) {
            return $query->notPublished();
        }

        if ((string) $value === self::PUBLISHED) {
            return $query->published();
        }

        return $query;
    }

    /**
     * Get the filter's available options.
     */
    public function options(NovaRequest $request): array
    {
        return [
            __('Published') => self::PUBLISHED,
            __('Draft') => self::DRAFT,
        ];
    }
}
