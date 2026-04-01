<?php

namespace Flobbos\Crudable\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Flobbos\Crudable\Exceptions\MissingRelationDataException;
use Flobbos\Crudable\Exceptions\MissingRequiredFieldsException;
use Flobbos\Crudable\Exceptions\MissingSlugFieldException;
use Flobbos\Crudable\Exceptions\MissingTranslationsException;
use Flobbos\Crudable\Exceptions\SlugNotFoundException;

class ExceptionsTest extends TestCase
{
    public function test_missing_relation_data_exception_message(): void
    {
        $e = new MissingRelationDataException('HasMany');
        $this->assertSame('HasMany is missing required related data.', $e->getMessage());
    }

    public function test_missing_relation_data_exception_no_type(): void
    {
        $e = new MissingRelationDataException();
        $this->assertStringContainsString('missing required related data', $e->getMessage());
    }

    public function test_missing_required_fields_exception_is_exception(): void
    {
        $e = new MissingRequiredFieldsException();
        $this->assertInstanceOf(\Exception::class, $e);
    }

    public function test_missing_slug_field_exception_is_exception(): void
    {
        $e = new MissingSlugFieldException('The slug_field is required');
        $this->assertSame('The slug_field is required', $e->getMessage());
    }

    public function test_missing_translations_exception_is_exception(): void
    {
        $e = new MissingTranslationsException();
        $this->assertInstanceOf(\Exception::class, $e);
    }

    public function test_slug_not_found_exception_is_exception(): void
    {
        $e = new SlugNotFoundException('my-slug does not exist.');
        $this->assertSame('my-slug does not exist.', $e->getMessage());
    }

    public function test_all_exceptions_extend_base_exception(): void
    {
        $exceptions = [
            MissingRelationDataException::class,
            MissingRequiredFieldsException::class,
            MissingSlugFieldException::class,
            MissingTranslationsException::class,
            SlugNotFoundException::class,
        ];

        foreach ($exceptions as $class) {
            $this->assertTrue(
                is_subclass_of($class, \Exception::class),
                "$class should extend Exception"
            );
        }
    }
}
