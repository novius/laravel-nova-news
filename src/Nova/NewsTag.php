<?php

namespace Novius\LaravelNovaNews\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Novius\LaravelNovaNews\NovaNews;
use Novius\LaravelNovaTranslatable\Nova\Actions\Translate;
use Novius\LaravelNovaTranslatable\Nova\Cards\Locales;
use Novius\LaravelNovaTranslatable\Nova\Fields\Locale;
use Novius\LaravelNovaTranslatable\Nova\Fields\Translations;
use Novius\LaravelNovaTranslatable\Nova\Filters\LocaleFilter;

class NewsTag extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\Novius\LaravelNovaNews\Models\NewsTag>
     */
    public static $model = \Novius\LaravelNovaNews\Models\NewsTag::class;

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

    public static $with = ['translations'];

    /**
     * Get the displayable label of the resource.
     */
    public static function label(): string
    {
        return trans('laravel-nova-news::crud-tag.resource_label');
    }

    /**
     * Get the displayable singular label of the resource.
     */
    public static function singularLabel(): string
    {
        return trans('laravel-nova-news::crud-tag.resource_label_singular');
    }

    public function availableLocales(): array
    {
        return NovaNews::getLocales();
    }

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make(trans('laravel-nova-news::crud-tag.name'), 'name')
                ->rules('required', 'max:255'),

            Slug::make(trans('laravel-nova-news::crud-tag.slug'), 'slug')
                ->from('name')
                ->rules('required', 'max:255'),

            Locale::make(trans('laravel-nova-news::crud-tag.language'), 'locale'),

            Translations::make(trans('laravel-nova-news::crud-tag.translations')),
        ];
    }

    /**
     * Get the cards available for the request.
     */
    public function cards(NovaRequest $request): array
    {
        return [
            new Locales(),
        ];
    }

    /**
     * Get the filters available for the resource.
     */
    public function filters(NovaRequest $request): array
    {
        return [
            new LocaleFilter(),
        ];
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
        if (count($this->availableLocales()) <= 1) {
            return [];
        }

        return [
            Translate::make()
                ->titleField('name')
                ->titleLabel(trans('laravel-nova-news::crud-tag.name'))
                ->onlyInline(),
        ];
    }
}
