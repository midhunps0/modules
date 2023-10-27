<?php

namespace Modules\Ynotz\MediaManager\Rules;

// use File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Modules\Ynotz\MediaManager\Models\MediaItem;
use Illuminate\Contracts\Validation\InvokableRule;

class MediaMimeTypes implements InvokableRule
{
    private $types;

    public function __construct($types)
    {
        $this->types = $types;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if (strpos($value, config('mediaManager.ulid_separator')) === false) {
            $fpath = Storage::disk(config('mediaManager.temp_disk'))
                ->path(config('mediaManager.temp_folder').'/'.$value);
        } else {
            $ulid = str_replace(config('mediaManager.ulid_separator'), '', $value);
            $mediaItem = MediaItem::where('ulid', $ulid)->get()->first();
            $fpath = $mediaItem->filepath;
        }

        if (!in_array(\File::extension($fpath), $this->types)) {
            $fail($value.':: '.__('- invalid type.'));
        }
    }
}
