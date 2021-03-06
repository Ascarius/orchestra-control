<?php namespace Orchestra\Control;

use Illuminate\Support\ServiceProvider;

class ControlServiceProvider extends ServiceProvider
{
    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the service provider
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__.'/../');

        $this->package('orchestra/control', 'orchestra/control', $path);

        $this->mapExtensionConfig();

        $this->bootExtensionEvents();

        $this->bootExtensionRouting($path);

        $this->bootExtensionMenuEvents();

        $this->bootTimezoneEvents();
    }

    /**
     * Boot extension events.
     *
     * @return void
     */
    protected function bootExtensionEvents()
    {
        $events  = $this->app['events'];
        $handler = 'Orchestra\Control\ExtensionConfigHandler';

        $events->listen('orchestra.form: extension.orchestra/control', "{$handler}@onViewForm");
        $events->listen('orchestra.saved: extension.orchestra/control', "{$handler}@onSaved");
    }

    /**
     * Boot extension menu handler.
     *
     * @return void
     */
    protected function bootExtensionMenuEvents()
    {
        $this->app['events']->listen('orchestra.ready: admin', 'Orchestra\Control\ControlMenuHandler');
    }

    /**
     * Boot extension routing.
     *
     * @param  string  $path
     * @return void
     */
    protected function bootExtensionRouting($path)
    {
        $this->app['router']->filter('control.manage', 'Orchestra\Foundation\Filters\CanManage');
        $this->app['router']->filter('control.csrf', 'Orchestra\Foundation\Filters\VerifyCsrfToken');

        require_once "{$path}/routes.php";
    }

    /**
     * Boot timezone events.
     *
     * @return void
     */
    protected function bootTimezoneEvents()
    {
        $events = $this->app['events'];
        $handler = 'Orchestra\Control\Timezone\UserHandler';

        $events->listen('orchestra.form: user.account', "{$handler}onViewForm");
        $events->listen('orchestra.saved: user.account', "{$handler}@onSaved");
    }

    /**
     * Map extension config.
     *
     * @return void
     */
    protected function mapExtensionConfig()
    {
        $this->app['orchestra.extension.config']->map('orchestra/control', [
            'localtime'   => 'orchestra/control::localtime.enable',
            'admin_role'  => 'orchestra/foundation::roles.admin',
            'member_role' => 'orchestra/foundation::roles.member'
        ]);
    }
}
