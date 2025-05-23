<?php

namespace Module\System\Providers;

use ReflectionProperty;
use Illuminate\Support\Arr;
use Monoland\Platform\DiscoverEvents;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function register()
    {
        static::setEventDiscoveryPaths([
            __DIR__ . '/../Listeners',
        ]);

        $events     = $this->discoverEvents();
        $provider   = Arr::first($this->app->getProviders(ServiceProvider::class));

        if (!$provider || empty($events)) {
            return;
        }

        $listen = new ReflectionProperty($provider, 'listen');
        $listen->setAccessible(true);
        $listen->setValue($provider, array_merge_recursive($listen->getValue($provider), $events));
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return true;
    }

    /**
     * Discover the events and listeners for the application.
     *
     * @return array
     */
    public function discoverEvents()
    {
        return collect($this->discoverEventsWithin())
            ->reject(function ($directory) {
                return ! is_dir($directory);
            })
            ->reduce(function ($discovered, $directory) {
                return array_merge_recursive(
                    $discovered,
                    DiscoverEvents::within($directory, $this->eventDiscoveryBasePath())
                );
            }, []);
    }
}
