<?php

namespace Flobbos\Crudable\Contracts;

interface Slugger
{
    /**
     * Get the ID of the Model being translated
     *
     * @param string $slug
     * @return integer
     * @throws \Flobbos\Crudable\Exceptions\SlugNotFoundException
     */
    public function getResourceIdFromTranslatedSlug(string $slug): int;

    /**
     * Get translated slug based on resource ID and language ID
     *
     * @param integer $id
     * @param integer $language_id
     * @return string
     * @throws \Flobbos\Crudable\Exceptions\SlugNotFoundException
     */
    public function getTranslatedSlugFromResourceId(int $id, int $language_id): string;

    /**
     * Get resource ID from a non-translated slug
     *
     * @param string $slug
     * @return integer
     * @throws \Flobbos\Crudable\Exceptions\SlugNotFoundException
     */
    public function getResourceIdFromSlug(string $slug): int;

    /**
     * Get the slug of a non-translated resource ID
     *
     * @param integer $id
     * @return string
     * @throws \Flobbos\Crudable\Exceptions\SlugNotFoundException
     */
    public function getSlugFromResourceId(int $id): string;
}
