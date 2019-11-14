<?php

namespace Flobbos\Crudable\Contracts;

interface Slugger {
    
    public function getResourceIdFromTranslatedSlug(string $slug, string $related_id): int;
    
    public function getTranslatedSlugFromResourceId(int $id, string $related_id, int $language_id = null): string;
    
    public function getResourceIdFromSlug(string $slug): int;
    
    public function getSlugFromResourceId(int $id): string;
    
}