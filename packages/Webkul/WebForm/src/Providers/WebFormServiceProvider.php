<?php

namespace Webkul\WebForm\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class WebFormServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/routes.php');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'web_form');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'web_form');

        Blade::anonymousComponentPath(__DIR__.'/../Resources/views/components');

        $this->registerVueSafeDirective();

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->app->register(ModuleServiceProvider::class);
    }

    /**
     * Print a stored value inside a Vue runtime template without letting it become Vue syntax.
     *
     * The public web form is rendered as a `<script type="text/template">` block that Vue compiles
     * at runtime, so a stored value is parsed as template source rather than as text. Blade's own
     * escaping does not help here: it leaves `{{` and `}}` untouched, and Vue decodes the HTML
     * entities it produces, so `{{constructor.constructor('...')()}}` saved as a form title became
     * an expression evaluated in the browser of every visitor — and of every site embedding the
     * form. Replacing the interpolation delimiters with their HTML-entity forms keeps the text
     * rendering identically while removing its meaning to the compiler.
     */
    protected function registerVueSafeDirective(): void
    {
        Blade::directive('vueSafe', function ($expression) {
            return "<?php echo \\Webkul\\WebForm\\Providers\\WebFormServiceProvider::escapeVueTemplate({$expression}); ?>";
        });
    }

    /**
     * Escape a value for inclusion in a runtime-compiled Vue template.
     */
    public static function escapeVueTemplate($value): string
    {
        $value = e((string) $value);

        return strtr($value, [
            '{{' => '&#123;&#123;',
            '}}' => '&#125;&#125;',
        ]);
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/Config/menu.php', 'menu.admin');

        $this->mergeConfigFrom(dirname(__DIR__).'/Config/acl.php', 'acl');
    }
}
