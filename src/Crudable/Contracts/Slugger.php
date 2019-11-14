<?php

namespace Flobbos\Crudable\Contracts;

interface Slugger {
    
    public function getResourceIdFromTranslatedSlug(string $slug, string $related_id): int;
    
    public function getTranslatedSlugFromResourceId(int $id, string $related_id): string;
    
    public function getResourceIdFromSlug(string $slug, int $language_id = null): int;
    
    public function getSlugFromResourceId(int $id): string;
    
}