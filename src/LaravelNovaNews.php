<?php

namespace Novius\LaravelNovaNews;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Tool;

class LaravelNovaNews extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     */
    public function boot(): void {}

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @return mixed
     */
    public function menu(Request $request)
    {
        $postResource = NovaNews::getPostResource();
        $tagResource = NovaNews::getTagResource();
        $categoryResource = NovaNews::getCategoryResource();

        return MenuSection::make('News', [
            MenuItem::make($postResource::label(), '/resources/'.$postResource::uriKey()),
            MenuItem::make($categoryResource::label(), '/resources/'.$categoryResource::uriKey()),
            MenuItem::make($tagResource::label(), '/resources/'.$tagResource::uriKey()),
        ])
            ->collapsable()
            ->icon('newspaper');
    }
}
