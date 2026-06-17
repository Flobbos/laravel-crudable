<?php

namespace Flobbos\Crudable\Tests\Feature;

use Flobbos\Crudable\CrudableServiceProvider;
use Flobbos\Crudable\Tests\TestCase;

class CrudableServiceProviderTest extends TestCase
{
    public function test_provider_registers_with_default_config(): void
    {
        // Default config is loaded by parent::setUp(); reaching this point
        // means the provider booted without throwing.
        $this->assertTrue(true);
    }

    public function test_provider_does_not_crash_when_implementations_is_null(): void
    {
        $this->app['config']->set('crudable.use_auto_binding', true);
        $this->app['config']->set('crudable.implementations', null);
        $this->app['config']->set('crudable.bindings', []);

        $provider = new CrudableServiceProvider($this->app);
        $provider->register();

        $this->assertTrue(true);
    }

    public function test_provider_does_not_crash_when_bindings_is_null(): void
    {
        $this->app['config']->set('crudable.use_auto_binding', true);
        $this->app['config']->set('crudable.implementations', []);
        $this->app['config']->set('crudable.bindings', null);

        $provider = new CrudableServiceProvider($this->app);
        $provider->register();

        $this->assertTrue(true);
    }

    public function test_provider_does_not_crash_when_both_keys_are_null(): void
    {
        $this->app['config']->set('crudable.use_auto_binding', true);
        $this->app['config']->set('crudable.implementations', null);
        $this->app['config']->set('crudable.bindings', null);

        $provider = new CrudableServiceProvider($this->app);
        $provider->register();

        $this->assertTrue(true);
    }
}
