<?php

namespace Novius\LaravelNovaNews\Nova;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource as NovaResource;
use Novius\LaravelMeta\Traits\NovaResourceHasMeta;
use Novius\LaravelNovaNews\Models\NewsCategory as NewsCategoryModel;
use Novius\LaravelNovaTranslatable\Nova\Cards\Locales;
use Novius\LaravelNovaTranslatable\Nova\Fields\Locale;
use Novius\LaravelNovaTranslatable\Nova\Fields\Translations;
use Novius\LaravelNovaTranslatable\Nova\Filters\LocaleFilter;

/**
 * @extends NovaResource<NewsCategoryModel>
 */
class NewsCategory extends NovaResource
{
    use NovaResourceHasMeta;

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<NewsCategoryModel>
     */
    public static string $model = NewsCategoryModel::class;

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
            new Panel(
                trans('laravel-nova-news::crud-category.panel_seo_fields'),
                $this->getSEONovaFields()
                    ->prepend(Heading::make(trans('laravel-nova-news::crud-category.seo_heading'))
                        ->asHtml()
                    )
                    ->pipe(function (Collection $fields) {
                        $position = $fields->values()->search(fn (Field $field) => Str::contains($field->attribute, 'og_'));
                        $before = $fields->slice(0, $position);
                        $after = $fields->slice($position);

                        $before->push(Heading::make(trans('laravel-nova-news::crud-category.og_heading'))
                            ->asHtml()
                        );

                        return $before->merge($after);
                    })
            ),
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

            Locale::make(),
            Translations::make(),
        ];
    }

    /**
     * Get the cards available for the request.
     */
    public function cards(NovaRequest $request): array
    {
        return [
            new Locales,
        ];
    }

    /**
     * Get the filters available for the resource.
     */
    public function filters(NovaRequest $request): array
    {
        return [
            new LocaleFilter,
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
        return [];
    }

    /**
     * Return a replicated resource.
     *
     *
     * @throws InvalidArgumentException
     */
    public function replicate(): static
    {
        return tap(parent::replicate(), function ($resource) {
            $model = $resource->model();

            $model->name .= ' (copy)';
            $model->slug = Str::slug($model->name);
        });
    }
}
