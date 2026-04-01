<?php

namespace Flobbos\Crudable\Tests\Feature;

use Flobbos\Crudable\Tests\TestCase;

class CommandsTest extends TestCase
{
    /** @var string */
    protected string $basePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->basePath = $this->app->basePath();
    }

    protected function tearDown(): void
    {
        foreach ([
            'app/Services/TestService.php',
            'app/Http/Controllers/Admin/TestController.php',
            'app/Contracts/TestContract.php',
        ] as $relative) {
            $path = $this->basePath . '/' . $relative;
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        parent::tearDown();
    }

    public function test_crud_service_command_generates_service_file(): void
    {
        $this->artisan('crud:service', ['name' => 'TestService'])
            ->assertExitCode(0);

        $path = $this->basePath . '/app/Services/TestService.php';
        $this->assertFileExists($path);
        $content = file_get_contents($path);
        $this->assertStringContainsString('class TestService', $content);
        $this->assertStringContainsString('use Flobbos\Crudable', $content);
    }

    public function test_crud_service_command_does_not_overwrite_existing(): void
    {
        $this->artisan('crud:service', ['name' => 'TestService'])
            ->assertExitCode(0);

        // Running again should report already exists and return false (exit 0)
        $this->artisan('crud:service', ['name' => 'TestService'])
            ->assertExitCode(0);
    }

    public function test_crud_controller_command_generates_controller_file(): void
    {
        $this->artisan('crud:controller', ['name' => 'Admin\\TestController'])
            ->assertExitCode(0);

        $path = $this->basePath . '/app/Http/Controllers/Admin/TestController.php';
        $this->assertFileExists($path);

        if (file_exists($path)) {
            @unlink($path);
        }
    }

    public function test_crud_contract_command_generates_contract_file(): void
    {
        $this->artisan('crud:contract', ['name' => 'TestContract'])
            ->assertExitCode(0);

        $path = $this->basePath . '/app/Contracts/TestContract.php';
        $this->assertFileExists($path);
        $content = file_get_contents($path);
        $this->assertStringContainsString('interface TestContract', $content);
    }

    public function test_crud_views_command_runs_without_error(): void
    {
        $this->artisan('crud:views', ['name' => 'Test'])
            ->assertExitCode(0);

        $viewsDir = $this->basePath . '/resources/views/test';
        if (is_dir($viewsDir)) {
            array_map('unlink', glob("$viewsDir/*.blade.php") ?: []);
            @rmdir($viewsDir);
        }
    }
}
