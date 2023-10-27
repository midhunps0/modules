<?php
namespace Modules\Ynotz\MediaManager\Services;

use Modules\Ynotz\MediaManager\Rules\MediaMaxSize;
use Illuminate\Support\Facades\Storage;
use Modules\Ynotz\MediaManager\Rules\MediaMimeTypes;

class EAInputMediaValidator
{
    private $rules = [];

    public function getRules(): array
    {
        return $this->rules;
    }

    public function maxSize(int $maxSize, string $unit)
    {
        $this->rules[] = new MediaMaxSize($maxSize, $unit);

        return $this;
    }

    public function mimeTypes(array $types)
    {
        $this->rules[] = new MediaMimeTypes($types);

        return $this;
    }
}
?>
