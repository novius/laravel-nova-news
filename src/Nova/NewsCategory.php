<?php

namespace Novius\LaravelNovaNews\Nova;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use Novius\LaravelNovaNews\Models\NewsCategory as NewsCategoryModel;
use Novius\LaravelNovaNews\NovaNews;
use Novius\LaravelNovaTranslatable\Nova\Actions\Translate;

class NewsCategory extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<NewsCategoryModel>
     */
    public static $model = NewsCategoryModel::class;

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

            new Panel(trans('laravel-nova-news::crud-category.panel_post_informations'), $this->mainFields()),
            new Panel(trans('laravel-nova-news::crud-category.panel_seo_fields'), $this->seoFields()),
            new Panel(trans('laravel-nova-news::crud-category.panel_og_fields'), $this->ogFields()),
        ];
    }

    protected function mainFields(): array
    {
        return [
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
                ->filterable()
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

    protected function seoFields(): array
    {
        return [
            Heading::make(trans('laravel-nova-news::crud-category.seo_heading'))
                ->asHtml(),

            Text::make(trans('laravel-nova-news::crud-category.seo_title'), 'seo_title')
                ->nullable()
                ->hideFromIndex(),

            Textarea::make(trans('laravel-nova-news::crud-category.seo_description'), 'seo_description'),
        ];
    }

    protected function ogFields(): array
    {
        return [
            Heading::make(trans('laravel-nova-news::crud-category.og_heading'))
                ->asHtml(),

            Text::make(trans('laravel-nova-news::crud-category.og_title'), 'og_title')
                ->nullable()
                ->hideFromIndex(),

            Textarea::make(trans('laravel-nova-news::crud-category.og_description'), 'og_description')
                ->nullable()
                ->hideFromIndex(),

            Image::make(trans('laravel-nova-news::crud-category.og_image'), 'og_image')
                ->nullable()
                ->hideFromIndex(),
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
        $locales = NovaNews::getLocales();
        if (count($locales) <= 1) {
            return [];
        }

        return [
            Translate::make()
                ->onModel($this->resource::class)
                ->locales($locales)
                ->titleField('name')
                ->titleLabel(trans('laravel-nova-news::crud-category.name'))
                ->onlyInline(),
        ];
    }

    /**
     * Return a replicated resource.
     *
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public function replicate()
    {
        return tap(parent::replicate(), function ($resource) {
            $model = $resource->model();

            $model->title = $model->title.' (copy)';
            $model->slug = Str::slug($model->title);
        });
    }
}
