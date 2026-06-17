<?php

namespace Flobbos\Crudable\Tests\Feature;

use Flobbos\Crudable\Contracts\Sluggable;
use Flobbos\Crudable\Crudable;
use Flobbos\Crudable\Exceptions\SlugNotFoundException;
use Flobbos\Crudable\Tests\TestCase;
use Flobbos\Crudable\Translations\Slugger;
use Illuminate\Database\Eloquent\Model;

class SluggerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['db']->connection()->getSchemaBuilder()->create('slug_articles', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('url_slug')->nullable();
            $table->timestamps();
        });

        $this->app['db']->connection()->getSchemaBuilder()->create('slug_article_translations', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('slug_article_id');
            $table->unsignedInteger('language_id');
            $table->string('name');
            $table->string('url_slug')->nullable();
            $table->timestamps();
        });
    }

    public function test_get_resource_id_from_slug_honors_custom_slug_name(): void
    {
        $article = SlugArticle::create(['name' => 'First', 'url_slug' => 'first-article']);

        $service = new SlugArticleService();
        $id = $service->getResourceIdFromSlug('first-article');

        $this->assertSame($article->id, $id);
    }

    public function test_get_resource_id_from_slug_throws_when_not_found(): void
    {
        $service = new SlugArticleService();

        $this->expectException(SlugNotFoundException::class);
        $service->getResourceIdFromSlug('does-not-exist');
    }

    public function test_get_slug_from_resource_id_honors_custom_slug_name(): void
    {
        $article = SlugArticle::create(['name' => 'Second', 'url_slug' => 'second-article']);

        $service = new SlugArticleService();
        $slug = $service->getSlugFromResourceId($article->id);

        $this->assertSame('second-article', $slug);
    }

    public function test_get_slug_from_resource_id_throws_when_not_found(): void
    {
        $service = new SlugArticleService();

        $this->expectException(SlugNotFoundException::class);
        $service->getSlugFromResourceId(999);
    }

    public function test_get_translated_slug_from_resource_id_honors_custom_slug_name(): void
    {
        $article = SlugArticle::create(['name' => 'Third']);
        SlugArticleTranslation::create([
            'slug_article_id' => $article->id,
            'language_id' => 1,
            'name' => 'Third EN',
            'url_slug' => 'third-en',
        ]);

        $service = new SlugArticleService();
        $slug = $service->getTranslatedSlugFromResourceId($article->id, 1);

        $this->assertSame('third-en', $slug);
    }

    public function test_get_translated_slug_from_resource_id_throws_when_not_found(): void
    {
        $service = new SlugArticleService();

        $this->expectException(SlugNotFoundException::class);
        $service->getTranslatedSlugFromResourceId(999, 1);
    }

    public function test_get_resource_id_from_translated_slug_honors_custom_slug_name(): void
    {
        $article = SlugArticle::create(['name' => 'Fourth']);
        SlugArticleTranslation::create([
            'slug_article_id' => $article->id,
            'language_id' => 1,
            'name' => 'Fourth EN',
            'url_slug' => 'fourth-en',
        ]);

        $service = new SlugArticleService();
        $foundId = $service->getResourceIdFromTranslatedSlug('fourth-en');

        $this->assertSame($article->id, $foundId);
    }
}

// ─── Support classes ──────────────────────────────────────────────────────────

class SlugArticle extends Model
{
    protected $table = 'slug_articles';
    protected $fillable = ['name', 'url_slug'];
    public $timestamps = true;
}

class SlugArticleTranslation extends Model
{
    protected $table = 'slug_article_translations';
    protected $fillable = ['slug_article_id', 'language_id', 'name', 'url_slug'];
    public $timestamps = true;
}

class SlugArticleService implements Sluggable
{
    use Crudable;
    use Slugger;

    protected $slug_field = 'name';
    protected $slug_name = 'url_slug';

    public function __construct()
    {
        $this->model = new SlugArticle();
    }
}
