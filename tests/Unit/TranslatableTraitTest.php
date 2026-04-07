<?php

namespace Flobbos\Crudable\Tests\Unit;

use Flobbos\Crudable\Exceptions\MissingRequiredFieldsException;
use Flobbos\Crudable\Tests\TestCase;
use Flobbos\Crudable\Translations\Translatable;

class TranslatableTraitTest extends TestCase
{
    protected function getService(): TranslatableStubService
    {
        return new TranslatableStubService();
    }

    public function test_process_translations_skips_non_array_items(): void
    {
        $service = $this->getService();
        $result = $service->processTranslations(['not-an-array', ['name' => 'Valid', 'language_id' => 1]]);

        $this->assertCount(1, $result);
        $this->assertSame('Valid', $result[0]['name']);
    }

    public function test_process_translations_removes_trans_key(): void
    {
        $service = $this->getService();
        $data = [
            ['id' => 5, 'name' => 'Test', 'language_id' => 1],
        ];

        $result = $service->processTranslations($data, 'id');

        $this->assertArrayNotHasKey('id', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
    }

    public function test_process_translations_filters_empty(): void
    {
        $service = $this->getService();
        $data = [
            ['language_id' => 1],
            ['name' => 'Real', 'language_id' => 2],
        ];

        $result = $service->processTranslations($data, null, 'language_id');
        $this->assertCount(1, $result);
    }

    public function test_filter_null_removes_empty_values(): void
    {
        $service = $this->getService();
        $result = $service->filterNull(['a' => 'value', 'b' => null, 'c' => '']);

        $this->assertArrayHasKey('a', $result);
        $this->assertArrayNotHasKey('b', $result);
        $this->assertArrayNotHasKey('c', $result);
    }

    public function test_check_required_returns_true_when_required_fields_present(): void
    {
        $service = $this->getService();
        $service->required_trans = ['name', 'language_id'];

        $result = $service->checkRequired(['name' => 'Hello', 'language_id' => 1, 'extra' => 'ignored']);

        $this->assertTrue($result);
    }

    public function test_check_required_returns_false_when_required_fields_missing(): void
    {
        $service = $this->getService();
        $service->required_trans = ['name', 'language_id'];

        $result = $service->checkRequired(['name' => 'Hello']);

        $this->assertFalse($result);
    }

    public function test_check_required_throws_when_required_trans_not_set(): void
    {
        $service = $this->getService();

        $this->expectException(MissingRequiredFieldsException::class);
        $service->checkRequired(['name' => 'Hello']);
    }

    public function test_generate_translated_slug(): void
    {
        $service = $this->getService();
        $slug = $service->generateTranslatedSlug('Hello World');

        $this->assertSame('hello-world', $slug);
    }
}

// ─── Support class ────────────────────────────────────────────────────────────

class TranslatableStubService
{
    use Translatable;

    public ?array $required_trans = null;
}
