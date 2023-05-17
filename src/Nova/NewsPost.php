<?php

namespace Novius\LaravelNovaNews\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
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
use Novius\LaravelNovaNews\Models\NewsPost as NewsPostModel;
use Novius\LaravelNovaNews\Nova\Actions\DraftPosts;
use Novius\LaravelNovaNews\Nova\Actions\PublishPosts;
use Novius\LaravelNovaNews\Nova\Filters\PostPublished;
use Waynestate\Nova\CKEditor4Field\CKEditor;

class NewsPost extends Resource
{
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
            new Panel(trans('laravel-nova-news::crud-post.panel_utility'), $this->utilityFields()),
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
                ->creationRules('required', 'string', 'max:191', 'postSlug', 'uniquePost:{{resourceLocale}}')
                ->updateRules('required', 'string', 'max:191', 'postSlug', 'uniquePost:{{resourceLocale}},{{resourceId}}')
                ->hideFromIndex(),

            Select::make(trans('laravel-nova-news::crud-post.language'), 'locale')
                ->options($this->getLocales())
                ->displayUsingLabels()
                ->rules('required', 'string', 'max:255')
                ->hideFromIndex(),

            Select::make(trans('laravel-nova-news::crud-post.status'), 'post_status')
                ->options([
                    NewsPostModel::STATUS_DRAFT => 'Draft',
                    NewsPostModel::STATUS_PUBLISHED => 'Published',
                ])
                ->default(NewsPostModel::STATUS_DRAFT)
                ->displayUsingLabels()
                ->hideFromIndex(),

            Tag::make(trans('laravel-nova-news::crud-post.categories'), 'categories', config('laravel-nova-news.category_resource'))
                ->showCreateRelationButton()
                ->preload()
                ->nullable()
                ->hideFromIndex(),

            Tag::make(trans('laravel-nova-news::crud-post.tags'), 'tags', config('laravel-nova-news.tag_resource'))
                ->showCreateRelationButton()
                ->preload()
                ->nullable()
                ->hideFromIndex(),

            DateTime::make(trans('laravel-nova-news::crud-post.publication_date'), 'publication_date')
                ->nullable()
                ->rules('required', 'date')
                ->hideFromIndex(),

            DateTime::make(trans('laravel-nova-news::crud-post.publication_end_date'), 'end_publication_date')
                ->nullable()
                ->rules('nullable', 'after:publication_date')
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

    /**
     * These fields are used to customize the Resource listing.
     * They're only displayed on the index view.
     */
    protected function utilityFields(): array
    {
        return [
            Text::make(trans('laravel-nova-news::crud-post.title'), 'title', function () {
                return '<span class="whitespace-nowrap" title="'.$this->resource->title.'">'.Str::limit($this->resource->title, 25).'</span>';
            })
                ->sortable()
                ->asHtml()
                ->onlyOnIndex(),

            Text::make(trans('laravel-nova-news::crud-post.preview_link'), function () {
                $previewUrl = $this->resource->previewUrl();

                return sprintf(
                    '<a class="link-default inline-flex items-center justify-start" href="%s" target="_blank">%s <svg class="inline-block ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg></a>',
                    $previewUrl,
                    trans('Open')
                );
            })
                ->asHtml()
                ->onlyOnIndex(),

            Boolean::make(trans('laravel-nova-news::crud-post.published'), function () {
                return $this->resource->isPublished();
            })->onlyOnIndex(),

            Boolean::make(trans('laravel-nova-news::crud-post.featured'), function () {
                return $this->resource->isFeatured();
            })->onlyOnIndex(),

            // Display Post status field on index
            Select::make(trans('laravel-nova-news::crud-post.status'), 'post_status')
                ->options([
                    NewsPostModel::STATUS_DRAFT => 'Draft',
                    NewsPostModel::STATUS_PUBLISHED => 'Published',
                ])
                ->default(NewsPostModel::STATUS_DRAFT)
                ->displayUsingLabels()
                ->sortable()
                ->onlyOnIndex(),

            Select::make(trans('laravel-nova-news::crud-post.language'), 'locale')
                ->options($this->getLocales())
                ->displayUsingLabels()
                ->sortable()
                ->onlyOnIndex(),
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

    protected function getLocales(): array
    {
        return config('laravel-nova-news.locales', []);
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
            new PostPublished(),
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
        return [
            new PublishPosts(),
            new DraftPosts(),
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
