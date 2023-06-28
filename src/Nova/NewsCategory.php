<?php

namespace Novius\LaravelNovaNews\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Novius\LaravelNovaNews\NovaNews;

class NewsCategory extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\Novius\LaravelNovaNews\Models\NewsCategory>
     */
    public static $model = \Novius\LaravelNovaNews\Models\NewsCategory::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
        'slug',
    ];

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * Get the displayable label of the resource.
     */
    public static function label(): string
    {
        return trans('laravel-nova-news::crud-category.resource_label');
    }

    /**
     * Get the displayable singular label of the resource.
     */
    public static function singularLabel(): string
    {
        return trans('laravel-nova-news::crud-category.resource_label_singular');
    }

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make(trans('laravel-nova-news::crud-category.name'), 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Slug::make(trans('laravel-nova-news::crud-category.slug'), 'slug')
                ->from('name')
                ->creationRules('required', 'string', 'max:191', 'newsSlug', 'uniqueCategory:{{resourceLocale}}')
                ->updateRules('required', 'string', 'max:191', 'newsSlug', 'uniqueCategory:{{resourceLocale}},{{resourceId}}')
                ->rules('required', 'max:255'),

            Select::make(trans('laravel-nova-news::crud-category.language'), 'locale')
                ->options(NovaNews::getLocales())
                ->displayUsingLabels()
                ->rules('required', 'string', 'max:255')
                ->sortable()
                ->showOnIndex(function () {
                    return count(NovaNews::getLocales()) > 1;
                })
                ->default(function () {
                    $locales = NovaNews::getLocales();
                    if (count($locales) === 1) {
                        return array_keys($locales)[0];
                    }

                    return null;
                }),
        ];
    }

    /**
     * Get the cards available for the request.
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
