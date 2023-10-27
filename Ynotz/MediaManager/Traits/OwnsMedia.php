<?php
namespace Modules\Ynotz\MediaManager\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Modules\Ynotz\MediaManager\Models\MediaItem;
use Modules\Ynotz\AccessControl\Models\Permission;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use stdClass;

trait OwnsMedia
{
    public function mediaPermissions($property = null, $variant = null)
    {
        $query = $this->morphToMany(Permission::class, 'mediaowner', 'media_permissions', 'mediaowner_id', 'permission_id');
        if (isset($property)) {
            $query->where('property', $property);
        }
        if (isset($variant)) {
            $query->where('variant', $variant);
        }
        return $query->get();
    }

    public function media()
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'mediaitem_id');
    }

    public function storageLocation(
        string $disk,
        string $folder
    ):array {
        return [
            'disk' => $disk,
            'folder' => $folder
        ];
    }

    public function attachMedia(MediaItem $mediaItem, string $property, array $customProps = []): void
    {
        $ulid = Str::ulid();
        if (count($customProps) > 0) {
            $this->media()->attach(
                $mediaItem,
                [
                    'id' => $ulid,
                    'property' => $property,
                    'custom_properties' => json_encode($customProps),
                    'created_by' => auth()->user()->id
                ]
            );
        } else {
            $this->media()->attach(
                $mediaItem,
                [
                    'id' => $ulid,
                    'property' => $property,
                    'created_by' => auth()->user()->id
                ]
            );
        }
    }

    public function addOneMediaFromEAInput(string $property, string $input): void
    {
        if (strpos($input, config('mediaManager.ulid_separator')) === false) {
            $arr = explode('_::_', $input);
            $ulid = $arr[0];
            $fname = $arr[1];
            $tempDisk = config('mediaManager.temp_disk');
            $tempFolder = config('mediaManager.temp_folder');

            $filepath = Storage::disk($tempDisk)->path($tempFolder.'/'.$input);
            $mimeType = mime_content_type($filepath);
            $fileType = explode('/', $mimeType)[0];
            $size = Storage::disk($tempDisk)->size($tempFolder.'/'.$input);

            $destFolder = '';
            $destDisk = '';

            if (isset($this->getMediaStorage()[$property])) {
                $destFolder = $this->getMediaStorage()[$property]['folder'] ?? '';
                $destDisk = $this->getMediaStorage()[$property]['disk'] ?? '';
            }

            if ($destDisk == '' || $destFolder == '') {
                switch($fileType) {
                    case 'image':
                        $destFolder = config('mediaManager.images_folder');
                        $destDisk = config('mediaManager.images_disk');
                        break;
                    case 'video':
                        $destFolder = config('mediaManager.videos_folder');
                        $destDisk = config('mediaManager.videos_disk');
                        break;
                    default:
                        $destFolder = config('mediaManager.files_folder');
                        $destDisk = config('mediaManager.files_disk');
                        break;
                }
            }

            $storagePath = $destFolder.'/'.$ulid.'/original/'.$fname;

            if (Storage::disk($tempDisk)->get($tempFolder.'/'.$input) == null) {
                throw new FileNotFoundException('Something went wrong. Couldn\'t save the '.$property.' file.');
            }

            Storage::disk($destDisk)->put(
                $destFolder.'/'.$ulid.'/original/'.$fname,
                Storage::disk($tempDisk)->get($tempFolder.'/'.$input)
            );

            Storage::disk($tempDisk)->delete($tempFolder.'/'.$input);
            $x = [
                'ulid' => $ulid,
                'filename' => $fname,
                'filepath' => $storagePath,
                'disk' => $destDisk,
                'type' => $fileType,
                'size' => $size, //size of the file in bytes
                'mime_type' => $mimeType,
            ];

            $mediaItem = MediaItem::create($x);

            $this->attachMedia($mediaItem, $property);

        } else {
            $ulid = str_replace(config('mediaManager.ulid_separator'), '', $input);
            $mediaItem = MediaItem::where('ulid', $ulid)->get()->first();

            if ($mediaItem != null) {
                $this->attachMedia($mediaItem, $property);
            }
        }

        // Do conversions if defined (check if conversions array exists)
        if (isset($this->getMediaVariants()[$property])) {
            if (isset($this->getMediaVariants()[$property]['process_on_upload']) && $this->getMediaVariants()[$property]['process_on_upload']) {
                //if queue available, queue job, else convert now
            }
        }
    }

    public function addMediaFromEAInput(string $property, array|string $vals): void
    {
        if (is_array($vals)) {
            foreach ($vals as $input) {
                $this->addOneMediaFromEAInput($property, $input);
            }
        } else {
            $this->addOneMediaFromEAInput($property, $vals);
        }
    }

    public function getAllMedia(string $property): Collection
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'mediaitem_id')->where('property', $property)->get();
    }
    // public function morphToMany($related, $name, $table = null, $foreignKey = null, $otherKey = null, $inverse = false){}

    public function getSingleMedia(string $property): MediaItem|null
    {
        return $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'mediaitem_id')->where('property', $property)->get()->first();
    }

    public function getSingleMediaFilePath(string $property): string|null
    {
        $m = $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'mediaitem_id')
            ->where('property', $property)
            ->get()->first();
        return $m ? $m->filepath : null;
    }

    public function getSingleMediaFileName(string $property): string|null
    {
        $m = $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'mediaitem_id')
            ->where('property', $property)
            ->get()->first();
        return $m ? $m->filename : null;
    }

    public function getSingleMediaForEAForm(string $property): array
    {
        return [
            'path' => $this->getSingleMediaUrl($property),
            'ulid' => $this->getSingleMediaUlid($property)
        ];
    }
    public function getSingleMediaUrl(string $property): string|null
    {
        $m = $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'mediaitem_id')
            ->where('property', $property)
            ->get()->first();
        return $m ? $m->url : null;
    }

    public function getSingleMediaUlid(string $property): string|null
    {
        $m = $this->morphToMany(MediaItem::class, 'mediaowner', 'media_instances', 'mediaowner_id', 'mediaitem_id')
            ->where('property', $property)
            ->get()->first();
        return $m ? $m->ulid : null;
    }

    public function getMediaVariants(): array
    {
        return [];
    }

    public function getMediaStorage(): array
    {
        return [];
    }

    public function deleteAllMedia(string $property): void
    {
        $this->media()->wherePivot('property', 'like', $property)
            ->detach();
    }

    public function syncMedia(string $property, $items)
    {
        $existing = [];
        $new = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if (strpos($item, config('mediaManager.ulid_separator')) === false) {
                    $new[] = $item;
                } else {
                    $ulid = str_replace(config('mediaManager.ulid_separator'), '', $item);
                    $mi = MediaItem::where('ulid', $ulid)->get()->first();
                    if (isset($mi)) {
                        $existing[] = $mi->id;
                    }
                }
            }
        } else {
            if (strpos($items, config('mediaManager.ulid_separator')) === false) {
                $new[] = $items;
            } else {
                $ulid = str_replace(config('mediaManager.ulid_separator'), '', $items);
                $mi = MediaItem::where('ulid', $ulid)->get()->first();
                if (isset($mi)) {
                    $existing[] = $mi->id;
                }
            }
        }

        $this->media()->wherePivot('property', $property)
            ->sync($existing);
        $this->addMediaFromEAInput($property, $new);
    }
}
?>
