<?php

namespace Novius\LaravelNovaNews\Nova;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Tag;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use Novius\LaravelNovaFieldPreview\Nova\Fields\OpenPreview;
use Novius\LaravelNovaNews\Models\NewsPost as NewsPostModel;
use Novius\LaravelNovaNews\NovaNews;
use Novius\LaravelNovaPublishable\Nova\Filters\PublicationStatus;
use Novius\LaravelNovaPublishable\Nova\Traits\Publishable;
use Novius\LaravelNovaTranslatable\Nova\Actions\Translate;
use Waynestate\Nova\CKEditor4Field\CKEditor;

class NewsPost extends Resource
{
    use Publishable;

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\Novius\LaravelNovaNews\Models\NewsPost>
     */
    public static $model = NewsPostModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title',
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
     *
     * @return string
     */
    public static function label()
    {
        return trans('laravel-nova-news::crud-post.resource_label');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return trans('laravel-nova-news::crud-post.resource_label_singular');
    }

    protected function fieldsForIndex(): array
    {
        return [
            Text::make(trans('laravel-nova-news::crud-post.title'), 'title', function () {
                return '<span class="whitespace-nowrap" title="'.$this->resource->title.'">'.Str::limit($this->resource->title, 25).'</span>';
            })
                ->sortable()
                ->asHtml(),

            OpenPreview::make(trans('laravel-nova-news::crud-post.preview_link')),

            ...$this->publishableDisplayFields(),

            Boolean::make(trans('laravel-nova-news::crud-post.featured'), function () {
                return $this->resource->isFeatured();
            }),

            Select::make(trans('laravel-nova-news::crud-post.language'), 'locale')
                ->options(NovaNews::getLocales())
                ->displayUsingLabels()
                ->sortable()
                ->filterable()
                ->showOnIndex(function () {
                    return count(NovaNews::getLocales()) > 1;
                }),
        ];
    }

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            new Panel(trans('laravel-nova-news::crud-post.panel_post_informations'), $this->mainFields()),
            new Panel(trans('laravel-nova-news::crud-post.panel_post_content'), $this->contentFields()),
            new Panel(trans('laravel-nova-news::crud-post.panel_seo_fields'), $this->seoFields()),
            new Panel(trans('laravel-nova-news::crud-post.panel_og_fields'), $this->ogFields()),
        ];
    }

    protected function mainFields(): array
    {
        return [
            Text::make(trans('laravel-nova-news::crud-post.title'), 'title')
                ->sortable()
                ->rules('required', 'string', 'max:255')
                ->hideFromIndex(),

            Slug::make(trans('laravel-nova-news::crud-post.slug'), 'slug')
                ->from('title')
                ->creationRules('required', 'string', 'max:191', 'newsSlug', 'uniquePost:{{resourceLocale}}')
                ->updateRules('required', 'string', 'max:191', 'newsSlug', 'uniquePost:{{resourceLocale}},{{resourceId}}')
                ->hideFromIndex(),

            Select::make(trans('laravel-nova-news::crud-post.language'), 'locale')
                ->options(NovaNews::getLocales())
                ->displayUsingLabels()
                ->sortable()
                ->filterable()
                ->rules('required', 'string', 'max:255')
                ->default(function () {
                    $locales = NovaNews::getLocales();
                    if (count($locales) === 1) {
                        return array_keys($locales)[0];
                    }

                    return null;
                })
                ->hideFromIndex(),

            ...$this->publishableFields(),

            Tag::make(trans('laravel-nova-news::crud-post.categories'), 'categories', config('laravel-nova-news.resources.category'))
                ->showCreateRelationButton()
                ->preload()
                ->nullable()
                ->hideFromIndex(),

            Tag::make(trans('laravel-nova-news::crud-post.tags'), 'tags', config('laravel-nova-news.resources.tag'))
                ->showCreateRelationButton()
                ->preload()
                ->nullable()
                ->hideFromIndex(),
        ];
    }

    protected function contentFields(): array
    {
        return [
            Boolean::make(trans('laravel-nova-news::crud-post.featured'), 'featured')
                ->hideFromIndex(),

            Textarea::make(trans('laravel-nova-news::crud-post.intro'), 'intro')
                ->nullable()
                ->help(trans('laravel-nova-news::crud-post.intro_help'))
                ->hideFromIndex(),

            CKEditor::make(trans('laravel-nova-news::crud-post.content'), 'content')
                ->options([
                    'toolbar' => [
                        ['Source', '-', 'Paste', 'PasteText'],
                        ['Undo', 'Redo', '-', 'RemoveFormat'],
                        ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'],
                        '/',
                        ['Bold', 'Italic', 'Strike', '-', 'Subscript', 'Superscript'],
                        ['NumberedList', 'BulletedList', '-', 'Blockquote'],
                        ['JustifyLeft', 'JustifyCenter', 'JustifyRight'],
                        ['Link', 'Unlink', 'Anchor'],
                        '/',
                        ['Format', 'FontSize'],
                        ['Maximize', 'ShowBlocks'],
                    ],
                ])
                ->nullable()
                ->hideFromIndex(),

            Image::make(trans('laravel-nova-news::crud-post.featured_image'), 'featured_image')
                ->nullable()
                ->hideFromIndex(),

            Image::make(trans('laravel-nova-news::crud-post.card_image'), 'card_image')
                ->help(trans('laravel-nova-news::crud-post.card_image_help'))
                ->nullable()
                ->hideFromIndex(),
        ];
    }

    protected function seoFields(): array
    {
        return [
            Heading::make(trans('laravel-nova-news::crud-post.seo_heading'))
                ->asHtml(),

            Text::make(trans('laravel-nova-news::crud-post.seo_title'), 'seo_title')
                ->nullable()
                ->hideFromIndex(),

            Textarea::make(trans('laravel-nova-news::crud-post.seo_description'), 'seo_description'),
        ];
    }

    protected function ogFields(): array
    {
        return [
            Heading::make(trans('laravel-nova-news::crud-post.og_heading'))
                ->asHtml(),

            Text::make(trans('laravel-nova-news::crud-post.og_title'), 'og_title')
                ->nullable()
                ->hideFromIndex(),

            Textarea::make(trans('laravel-nova-news::crud-post.og_description'), 'og_description')
                ->nullable()
                ->hideFromIndex(),

            Image::make(trans('laravel-nova-news::crud-post.og_image'), 'og_image')
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
        return [
            new PublicationStatus(),
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
        $locales = NovaNews::getLocales();
        if (count($locales) <= 1) {
            return [];
        }

        return [
            Translate::make()
                ->onModel($this->resource::class)
                ->locales($locales)
                ->titleField('title')
                ->titleLabel(trans('laravel-nova-news::crud-post.title'))
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
