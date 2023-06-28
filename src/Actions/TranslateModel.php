<?php

namespace Novius\LaravelNovaNews\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Novius\LaravelNovaNews\NovaNews;

class TranslateModel extends Action
{
    protected ?string $onModel = null;

    protected string $titleField = 'title';

    public function onModel(string $model): static
    {
        $this->onModel = $model;

        return $this;
    }

    public function titleField(string $titleField): static
    {
        $this->titleField = $titleField;

        return $this;
    }

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if ($this->onModel === null || ! class_exists($this->onModel)) {
            throw new \RuntimeException('You must define Model of TranslateModel action.');
        }

        if ($models->count() > 1) {
            return Action::danger(trans('laravel-nova-news::errors.action_only_available_for_single'));
        }

        $modelToTranslate = $models->first();
        $locale = $fields->locale;
        if ($modelToTranslate->locale === $locale) {
            return Action::danger(trans('laravel-nova-news::errors.already_translated'));
        }

        if (! empty($modelToTranslate->locale_parent_id)) {
            $modelToTranslate = $modelToTranslate->localParent;
            if (empty($modelToTranslate)) {
                return Action::danger(trans('laravel-nova-news::errors.error_during_translation'));
            }
        }

        $otherPageAlreadyExists = $this->onModel::query()
            ->where('locale', $locale)
            ->where('locale_parent_id', $modelToTranslate->id)
            ->exists();

        if ($otherPageAlreadyExists) {
            return Action::danger(trans('laravel-nova-news::errors.already_translated'));
        }

        $translatedItem = $modelToTranslate->replicate();
        $translatedItem->{$this->titleField} = $fields->title;
        $translatedItem->slug = null;
        $translatedItem->locale = $locale;
        $translatedItem->locale_parent_id = $modelToTranslate->id;

        if (! $translatedItem->save()) {
            return Action::danger(trans('laravel-nova-news::errors.error_during_translation'));
        }

        return Action::message(trans('laravel-nova-news::menu.successfully_translated_menu'));
    }

    /**
     * Get the fields available on the action.
     */
    public function fields(NovaRequest $request): array
    {
        $locales = NovaNews::getLocales();

        return [
            Text::make(trans('laravel-nova-news::crud-post.title'), 'title')
                ->required()
                ->rules('required', 'max:255'),

            Select::make(trans('laravel-nova-news::crud-post.locale'), 'locale')
                ->options($locales)
                ->displayUsingLabels()
                ->rules('required', 'in:'.implode(',', array_keys($locales))),
        ];
    }
}
