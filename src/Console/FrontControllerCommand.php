<?php

namespace Novius\LaravelNovaNews\Console;

use Illuminate\Console\GeneratorCommand;

class FrontControllerCommand extends GeneratorCommand
{
    protected $signature = 'news-manager:publish-front {--without-categories} {--without-tags}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate front controller and add route for news';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    public function handle(): void
    {
        if (parent::handle() !== false) {
            if (! is_file(base_path('routes/web.php'))) {
                $this->warn('There is no routes/web.php file. Abort without generated new route.');

                return;
            }

            $routeToAppend = file_get_contents(__DIR__.'/stubs/routes.front.stub');
            $routeToAppend = str_replace([
                '{{frontPostsRouteName}}',
                '{{frontCategoriesRouteName}}',
                '{{frontPostRouteName}}',
                '{{frontCategoryRouteName}}',
                '{{frontTagRouteName}}',
                '{{frontPostParameterName}}',
                '{{frontCategoryParameterName}}',
                '{{frontTagParameterName}}',
                '{{withoutCategories}}',
                '{{withoutTags}}',
            ], [
                config('laravel-nova-news.front_routes_name.posts') ?? 'news.posts',
                config('laravel-nova-news.front_routes_name.categories') ?? 'news.categories',
                config('laravel-nova-news.front_routes_name.post') ?? 'news.post',
                config('laravel-nova-news.front_routes_name.category') ?? 'news.category',
                config('laravel-nova-news.front_routes_name.tag') ?? 'news.tag',
                config('laravel-nova-news.front_routes_parameters.post') ?? 'post',
                config('laravel-nova-news.front_routes_parameters.category') ?? 'category',
                config('laravel-nova-news.front_routes_parameters.tag') ?? 'tag',
                $this->option('without-categories') ? '// ' : '',
                $this->option('without-tags') ? '// ' : '',
            ], $routeToAppend);

            file_put_contents(
                base_path('routes/web.php'),
                $routeToAppend,
                FILE_APPEND
            );
        }
    }

    /**
     * Get the desired class name from the input.
     */
    protected function getNameInput(): string
    {
        return 'FrontNewsController';
    }

    /**
     * Resolve the fully-qualified path to the stub.
     */
    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/controller.front.stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Http\Controllers';
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [];
    }

    protected function replaceClass($stub, $name): string
    {
        $stub = str_replace([
            '{{frontPostParameterName}}',
            '{{frontCategoryParameterName}}',
            '{{frontTagParameterName}}',
        ], [
            config('laravel-nova-news.front_routes_parameters.post') ?? 'post',
            config('laravel-nova-news.front_routes_parameters.category') ?? 'category',
            config('laravel-nova-news.front_routes_parameters.tag') ?? 'tag',
        ], $stub);

        return parent::replaceClass($stub, $name);
    }
}
