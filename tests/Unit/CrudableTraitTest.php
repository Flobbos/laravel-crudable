<?php

namespace Flobbos\Crudable\Tests\Unit;

use Flobbos\Crudable\Crudable;
use Flobbos\Crudable\Exceptions\InvalidUploadException;
use Flobbos\Crudable\Tests\TestCase;
use Illuminate\Http\Request;

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

    public function test_delete_hard_deletes_record(): void
    {
        $model = StubModel::create(['name' => 'ToHardDelete']);
        $service = $this->getService();
        $service->delete($model->id, true);

        $this->assertDatabaseMissing('stub_models', ['id' => $model->id]);
    }

    public function test_delete_hard_deletes_already_trashed_record(): void
    {
        $model = StubModel::create(['name' => 'ToForceDelete']);
        $model->delete();
        $this->assertSoftDeleted('stub_models', ['id' => $model->id]);

        $service = $this->getService();
        $service->delete($model->id, true);

        $this->assertDatabaseMissing('stub_models', ['id' => $model->id]);
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

    public function test_handle_upload_throws_invalid_upload_exception_when_no_file_present(): void
    {
        $service = $this->getService();
        $request = Request::create('/upload', 'POST');

        $this->expectException(InvalidUploadException::class);
        $service->handleUpload($request, 'photo');
    }

    public function test_handle_upload_exception_remains_catchable_as_base_exception(): void
    {
        $service = $this->getService();
        $request = Request::create('/upload', 'POST');

        $caught = false;
        try {
            $service->handleUpload($request, 'photo');
        } catch (\Exception $e) {
            $caught = true;
        }

        $this->assertTrue($caught, 'handleUpload exception must remain catchable as \Exception');
    }

    // ─── Query state isolation regression tests ─────────────────────────────

    public function test_where_does_not_leak_state_across_calls(): void
    {
        StubModel::create(['name' => 'Alpha']);
        StubModel::create(['name' => 'Beta']);

        $service = $this->getService();

        $filtered = $service->where('name', 'Alpha')->get();
        $this->assertCount(1, $filtered);

        // Without the fix this would still filter by name=Alpha and return 1.
        $all = $service->get();
        $this->assertCount(2, $all);
    }

    public function test_orderby_does_not_leak_state_across_calls(): void
    {
        StubModel::create(['name' => 'Zebra']);
        StubModel::create(['name' => 'Apple']);

        $service = $this->getService();

        $ascending = $service->orderBy('name', 'asc')->get();
        $this->assertSame('Apple', $ascending->first()->name);

        $descending = $service->orderBy('name', 'desc')->get();
        $this->assertSame('Zebra', $descending->first()->name);
    }

    public function test_chained_where_orderby_get_works_together(): void
    {
        StubModel::create(['name' => 'Zebra']);
        StubModel::create(['name' => 'Apple']);
        StubModel::create(['name' => 'Charlie']);

        $service = $this->getService();
        $results = $service->where('id', '>', 0)->orderBy('name')->get();

        $this->assertCount(3, $results);
        $this->assertSame('Apple', $results->first()->name);
    }

    public function test_raw_returns_model_after_chained_terminal_call(): void
    {
        StubModel::create(['name' => 'Anything']);

        $service = $this->getService();
        $service->where('name', 'Anything')->get();

        // Without the fix, $this->model would be a Builder by now.
        $this->assertInstanceOf(StubModel::class, $service->raw());
    }

    public function test_find_uses_pending_chain_when_present(): void
    {
        $alpha = StubModel::create(['name' => 'Alpha']);
        $beta = StubModel::create(['name' => 'Beta']);

        $service = $this->getService();

        // Restricting by name='Beta' should make find($alpha->id) return null,
        // because the row with that id has name='Alpha'.
        $found = $service->where('name', 'Beta')->find($alpha->id);
        $this->assertNull($found);

        // After consumption the chain is gone — find by id alone should work.
        $foundAgain = $service->find($alpha->id);
        $this->assertNotNull($foundAgain);
        $this->assertSame($alpha->id, $foundAgain->id);
    }

    public function test_create_does_not_leak_pending_chain_state(): void
    {
        StubModel::create(['name' => 'Existing']);

        $service = $this->getService();

        // A pending where that we never terminate.
        $service->where('id', 999);

        // Create should ignore the pending chain.
        $service->create(['name' => 'New']);

        // And the next get() should not be filtered by id=999.
        $all = $service->get();
        $this->assertCount(2, $all);
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
