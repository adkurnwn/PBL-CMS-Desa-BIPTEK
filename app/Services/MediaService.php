<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class MediaService
{
    public function upload($file, $path, $disk = 'public')
    {
        $filename = uniqid() . '.webp';
        $fullPath = $path . '/' . $filename;

        if (str_starts_with($file->getMimeType(), 'image/')) {
            $image = Image::make($file)
                ->resize(1600, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80);

            Storage::disk($disk)->put($fullPath, (string) $image);

            $absolutePath = Storage::disk($disk)->path($fullPath);
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize($absolutePath);
        } else {
            $file->storeAs($path, $file->getClientOriginalName(), $disk);
        }

        return $fullPath;
    }

    public function uploadMultiple($files, $path, $disk = 'public')
    {
        $paths = [];
        foreach ($files as $file) {
            $paths[] = $this->upload($file, $path, $disk);
        }
        return $paths;
    }

    public function delete($path, $disk = 'public')
    {
        if (empty($path)) {
            return false;
        }
        return Storage::disk($disk)->delete($path);
    }

    public function deleteMultiple($paths, $disk = 'public')
    {
        $results = [];
        foreach ($paths as $path) {
            $results[$path] = $this->delete($path, $disk);
        }
        return $results;
    }

    public function handleImageUploads($model, $images, $alts = [], $path = null, $disk = 'public')
    {
        if (!$path) {
            $className = class_basename($model);
            $path = strtolower($className) . '-images';
        }

        $createdImages = [];
        $defaultAltText = $model->title ?? $model->name ?? 'Image';

        foreach ($images as $index => $image) {
            $filePath = $this->upload($image, $path, $disk);

            $altText = isset($alts[$index]) && !empty(trim($alts[$index]))
                ? $alts[$index]
                : $defaultAltText;

            $imageData = [
                'path' => $filePath,
                'alt' => $altText,
                'is_visible' => true
            ];

            if (method_exists($model, 'images')) {
                $createdImages[] = $model->images()->create($imageData);
            } elseif (method_exists($model, 'gambarProduk')) {
                $createdImages[] = $model->gambarProduk()->create($imageData);
            }
        }

        return $createdImages;
    }

    public function handleImageRemovals($model, $imageIds, $relationshipColumn = null, $disk = 'public')
    {
        $count = 0;
        $modelClass = get_class($model);
        $modelId = $model->id;

        if (!$relationshipColumn) {
            $className = strtolower(class_basename($model));
            $relationshipColumn = $className . '_id';
        }

        $imageModelClass = $this->getImageModelClass($model);

        foreach ($imageIds as $imageId) {
            $image = $imageModelClass::where('id', $imageId)
                ->where($relationshipColumn, $modelId)
                ->first();

            if ($image) {
                $this->delete($image->path, $disk);
                $image->delete();
                $count++;
            }
        }

        return $count;
    }

    protected function getImageModelClass($model)
    {
        $className = class_basename($model);

        switch ($className) {
            case 'ProdukDesa':
                return \App\Models\GambarProdukDesa::class;
            case 'News':
            case 'Gallery':
                return \App\Models\Image::class;
            default:
                return \App\Models\Image::class;
        }
    }

    public function handleVideoUpload($model, $video, $path = null, $disk = 'public', $column = 'video')
    {
        if (!$path) {
            $className = strtolower(class_basename($model));
            $path = $className . '-videos';
        }

        if (!empty($model->$column)) {
            $this->delete($model->$column, $disk);
        }

        $videoPath = $this->upload($video, $path, $disk);
        $model->update([$column => $videoPath]);

        return $videoPath;
    }

    public function handleJsonImages($model, $files, $column, $path, $disk = 'public')
    {
        $images = json_decode($model->$column) ?? [];

        foreach ($files as $index => $file) {
            if (isset($images[$index])) {
                $this->delete($images[$index], $disk);
            }

            $storedPath = $this->upload($file, $path, $disk);
            $images[$index] = $storedPath;
        }

        $images = array_values($images);
        $model->update([$column => json_encode($images)]);

        return $images;
    }

    public function deleteAllMedia($model)
    {
        foreach ($model->images as $image) {
            $this->delete($image->path);
            $image->delete();
        }

        if ($model->video) {
            $this->delete($model->video);
        }
    }

    public function handleImageAltTextUpdates($model, $updatedImages)
    {
        $count = 0;
        $defaultAltText = $model->title ?? $model->name ?? 'Image';

        foreach ($updatedImages as $updatedImage) {
            if (isset($updatedImage['id'])) {
                $image = $model->images()->find($updatedImage['id']);

                if ($image) {
                    $altText = (isset($updatedImage['alt']) && !empty(trim($updatedImage['alt'])))
                        ? $updatedImage['alt']
                        : $defaultAltText;

                    $image->update(['alt' => $altText]);
                    $count++;
                }
            }
        }

        return $count;
    }

    public function handleDocumentUpload($file, $title, $path, $existingFilePath = null, $disk = 'public')
    {
        if ($existingFilePath) {
            $this->delete($existingFilePath, $disk);
        }

        $sanitizedTitle = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $title);
        $extension = $file->getClientOriginalExtension();
        $filename = $sanitizedTitle . '.' . $extension;

        $filePath = $file->storeAs($path, $filename, $disk);

        return $filePath;
    }
}
