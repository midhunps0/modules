<?php

namespace Modules\Ynotz\MediaManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\Ynotz\MediaManager\Models\GalleryItem;
use Modules\Ynotz\SmartPages\Http\Controllers\SmartController;

class MediaController extends SmartController
{
    public function fileUpload(Request $request)
    {
        $file = $this->request->file('file');

        $name = $file->getClientOriginalName();
        $name = str_replace($file->getClientOriginalExtension(), '', $name);
        $name = Str::swap(
            [
                config('mediaManager.ulid_separator') => '',
                config('mediaManager.tmp_separator') => '',
                ' ' => '_',
                '.' =>'',
                '-' => ''
            ],
            $name
        );
        $name = time().rand(0,99).config('mediaManager.tmp_separator').substr($name, 0, 20).'.'.$file->extension();

        $tempFolder = config('mediaManager.temp_folder');
        $tempDisk = config('mediaManager.temp_disk');
        $path = trim($file->storeAs($tempFolder.'/', $name, $tempDisk));

        $data = [
            'name' => $name,
            'url' => Storage::disk($tempDisk)->url($path)
        ];

        $addToGallery = $request->input('add_to_gallery');

        if ($addToGallery != null && $addToGallery == true) {
            $gi = new GalleryItem();
            $gi->save();
            $gi->refresh();
            $gi->addOneMediaFromEAInput('image', $name);
            $imageData = $gi->image;
            $data = [
                'name' => $gi->getSingleMediaFileName('image'),
                'url' => $imageData['path'],
                'ulid' => $imageData['ulid'],
            ];
        }

        return response()->json(
            $data
        );
    }

    public function fileDelete()
    {
        $tempFolder = config('mediaManager.temp_folder');
        $tempDisk = config('mediaManager.temp_disk');
        info($this->request->input('file'));
        Storage::disk($tempDisk)->delete($tempFolder.'/'.trim($this->request->input('file')));
        return response()->json([
            'success' => true
        ]);
    }

    public function gallery()
    {
        $galleryService = config('mediaManager.gallery_service');

        return response()->json([
            'items' => (new $galleryService)->getMediaItems()
        ]);
    }

    public function displayImage($variant, $ulid, $imagename)
    {
        $path = storage_path('images/' . $ulid.'/'.$variant.'/'.$imagename);

        if (!File::exists($path)) {
            ImageService::makeVariant($variant, $ulid);
        }

        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    }
}
