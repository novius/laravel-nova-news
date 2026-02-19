<?php

namespace Novius\LaravelNovaNews\Nova;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Tag;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource as NovaResource;
use Novius\LaravelMeta\Traits\NovaResourceHasMeta;
use Novius\LaravelNovaFieldPreview\Nova\Fields\OpenPreview;
use Novius\LaravelNovaNews\Models\NewsPost as NewsPostModel;
use Novius\LaravelNovaPublishable\Nova\Fields\ExpiredAt;
use Novius\LaravelNovaPublishable\Nova\Fields\PublicationBadge;
use Novius\LaravelNovaPublishable\Nova\Fields\PublicationStatus as PublicationStatusField;
use Novius\LaravelNovaPublishable\Nova\Fields\PublishedAt;
use Novius\LaravelNovaPublishable\Nova\Fields\PublishedFirstAt;
use Novius\LaravelNovaPublishable\Nova\Filters\PublicationStatus;
use Novius\LaravelNovaTranslatable\Nova\Cards\Locales;
use Novius\LaravelNovaTranslatable\Nova\Fields\Locale;
use Novius\LaravelNovaTranslatable\Nova\Fields\Translations;
use Novius\LaravelNovaTranslatable\Nova\Filters\LocaleFilter;
use Waynestate\Nova\CKEditor4Field\CKEditor;

/**
 * @extends NovaResource<NewsPostModel>
 */
class NewsPost extends NovaResource
{
    use NovaResourceHasMeta;

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<NewsPostModel>
     */
    public static string $model = NewsPostModel::class;

    public static $title = 'title';

    public static $search = [
        'title',
        'slug',
    ];

    public static $displayInNavigation = false;

    public static $with = ['translationsWithDeleted'];

    public static function label(): string
    {
        return trans('laravel-nova-news::crud-post.resource_label');
    }

    public static function singularLabel(): string
    {
        return trans('laravel-nova-news::crud-post.resource_label_singular');
    }

    protected function publicationFields(): array
    {
        return [
            PublicationBadge::make(),
            PublicationStatusField::make()->onlyOnForms(),
            PublishedFirstAt::make()->hideFromIndex(),
            PublishedAt::make()->onlyOnForms(),
            ExpiredAt::make()->onlyOnForms(),
        ];
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

            ...$this->publicationFields(),

            Boolean::make(trans('laravel-nova-news::crud-post.featured'), function () {
                return $this->resource->isFeatured();
            }),

            Locale::make(),
            Translations::make(),
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
            new Panel(
                trans('laravel-nova-news::crud-post.panel_seo_fields'),
                $this->getSEONovaFields()
                    ->prepend(Heading::make(trans('laravel-nova-news::crud-post.seo_heading'))
                        ->asHtml()
                    )
                    ->pipe(function (Collection $fields) {
                        $position = $fields->values()->search(fn (Field $field) => Str::contains($field->attribute, 'og_'));
                        $before = $fields->slice(0, $position);
                        $after = $fields->slice($position);

                        $before->push(Heading::make(trans('laravel-nova-news::crud-post.og_heading'))
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
            Text::make(trans('laravel-nova-news::crud-post.title'), 'title')
                ->sortable()
                ->rules('required', 'string', 'max:255')
                ->hideFromIndex(),

            Slug::make(trans('laravel-nova-news::crud-post.slug'), 'slug')
                ->from('title')
                ->creationRules('required', 'string', 'max:191', 'newsSlug', 'uniquePost:{{resourceLocale}}')
                ->updateRules('required', 'string', 'max:191', 'newsSlug', 'uniquePost:{{resourceLocale}},{{resourceId}}')
                ->hideFromIndex(),

            Locale::make(),
            Translations::make(),

            ...$this->publicationFields(),

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
            new PublicationStatus,
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
