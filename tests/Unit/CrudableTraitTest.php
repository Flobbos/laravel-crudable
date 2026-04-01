<?php

namespace Flobbos\Crudable\Tests\Unit;

use Flobbos\Crudable\Tests\TestCase;
use Flobbos\Crudable\Crudable;
use Flobbos\Crudable\Exceptions\MissingSlugFieldException;
use Illuminate\Support\Str;

class CrudableTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase();
    }

    protected function setUpDatabase(): void
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('stub_models', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    protected function getService(): StubCrudService
    {
        return new StubCrudService();
    }

    public function test_raw_returns_model(): void
    {
        $service = $this->getService();
        $this->assertInstanceOf(StubModel::class, $service->raw());
    }

    public function test_get_returns_all_models(): void
    {
        StubModel::create(['name' => 'Item A']);
        StubModel::create(['name' => 'Item B']);

        $service = $this->getService();
        $results = $service->get();

        $this->assertCount(2, $results);
    }

    public function test_get_with_id_returns_single_model(): void
    {
        $model = StubModel::create(['name' => 'Single']);
        $service = $this->getService();

        $found = $service->get($model->id);
        $this->assertSame('Single', $found->name);
    }

    public function test_create_inserts_record(): void
    {
        $service = $this->getService();
        $model = $service->create(['name' => 'Created']);

        $this->assertDatabaseHas('stub_models', ['name' => 'Created']);
        $this->assertSame('Created', $model->name);
    }

    public function test_update_changes_record(): void
    {
        $model = StubModel::create(['name' => 'Original']);
        $service = $this->getService();
        $service->update($model->id, ['name' => 'Updated']);

        $this->assertDatabaseHas('stub_models', ['name' => 'Updated']);
    }

    public function test_update_returns_model_when_requested(): void
    {
        $model = StubModel::create(['name' => 'ToUpdate']);
        $service = $this->getService();
        $result = $service->update($model->id, ['name' => 'AfterUpdate'], true);

        $this->assertInstanceOf(StubModel::class, $result);
        $this->assertSame('AfterUpdate', $result->name);
    }

    public function test_delete_soft_deletes_record(): void
    {
        $model = StubModel::create(['name' => 'ToDelete']);
        $service = $this->getService();
        $service->delete($model->id);

        $this->assertSoftDeleted('stub_models', ['id' => $model->id]);
    }

    public function test_restore_undeletes_record(): void
    {
        $model = StubModel::create(['name' => 'ToRestore']);
        $model->delete();

        $service = $this->getService();
        $service->restore($model->id);

        $this->assertDatabaseHas('stub_models', ['id' => $model->id, 'deleted_at' => null]);
    }

    public function test_first_returns_first_record(): void
    {
        StubModel::create(['name' => 'First']);
        StubModel::create(['name' => 'Second']);

        $service = $this->getService();
        $result = $service->first();

        $this->assertSame('First', $result->name);
    }

    public function test_where_filters_records(): void
    {
        StubModel::create(['name' => 'Alpha']);
        StubModel::create(['name' => 'Beta']);

        $service = $this->getService();
        $results = $service->where('name', 'Alpha')->get();

        $this->assertCount(1, $results);
        $this->assertSame('Alpha', $results->first()->name);
    }

    public function test_paginate_returns_paginator(): void
    {
        StubModel::create(['name' => 'Page1']);
        StubModel::create(['name' => 'Page2']);

        $service = $this->getService();
        $result = $service->paginate(1);

        $this->assertSame(1, $result->count());
        $this->assertSame(2, $result->total());
    }

    public function test_generate_slug_returns_slugified_string(): void
    {
        $service = $this->getService();
        $slug = $service->generateSlug('Hello World');

        $this->assertSame('hello-world', $slug);
    }

    public function test_get_trash_returns_soft_deleted(): void
    {
        $model = StubModel::create(['name' => 'Trashed']);
        $model->delete();

        $service = $this->getService();
        $trash = $service->getTrash();

        $this->assertCount(1, $trash);
        $this->assertSame('Trashed', $trash->first()->name);
    }

    public function test_order_by_sorts_results(): void
    {
        StubModel::create(['name' => 'Zebra']);
        StubModel::create(['name' => 'Apple']);

        $service = $this->getService();
        $results = $service->orderBy('name')->get();

        $this->assertSame('Apple', $results->first()->name);
    }
}

// ─── Support classes ─────────────────────────────────────────────────────────

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StubModel extends Model
{
    use SoftDeletes;

    protected $table = 'stub_models';
    protected $fillable = ['name', 'slug'];
}

class StubCrudService
{
    use Crudable;

    public function __construct()
    {
        $this->model = new StubModel();
    }
}
