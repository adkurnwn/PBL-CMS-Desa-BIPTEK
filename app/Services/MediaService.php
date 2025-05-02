<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function upload($file, $path, $disk = 'public')
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $fullPath = $path . '/' . $filename;

        if (str_starts_with($file->getMimeType(), 'image/')) {
            $image = Image::make($file)
                ->resize(1600, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode($file->getClientOriginalExtension(), 75);

            Storage::disk($disk)->put($fullPath, $image);
        } else {
            $file->storeAs($path, $filename, $disk);
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
        // Determine the appropriate storage path based on model type
        if (!$path) {
            $className = class_basename($model);
            $path = strtolower($className) . '-images';
        }

        $createdImages = [];

        foreach ($images as $index => $image) {
            $filePath = $this->upload($image, $path, $disk);

            // Create the image record using the appropriate relationship
            $imageData = [
                'path' => $filePath,
                'alt' => $alts[$index] ?? $model->title ?? $model->name ?? 'Image',
                'is_visible' => true
            ];

            // Different models have different relationship methods
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

        // If relationship column not specified, determine from model class name
        if (!$relationshipColumn) {
            $className = strtolower(class_basename($model));
            $relationshipColumn = $className . '_id';
        }

        // Get the appropriate image model class
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
        // Determine path if not specified
        if (!$path) {
            $className = strtolower(class_basename($model));
            $path = $className . '-videos';
        }

        // Delete existing video if present
        if (!empty($model->$column)) {
            $this->delete($model->$column, $disk);
        }

        // Upload new video
        $videoPath = $this->upload($video, $path, $disk);

        // Update model with new path
        $model->update([$column => $videoPath]);

        return $videoPath;
    }

    public function handleJsonImages($model, $files, $column, $path, $disk = 'public')
    {
        // Get existing images from JSON
        $images = json_decode($model->$column) ?? [];

        // Process each file
        foreach ($files as $index => $file) {
            // If an image already exists at this position, delete it
            if (isset($images[$index])) {
                $this->delete($images[$index], $disk);
            }

            // Store new image
            $path = $this->upload($file, $path, $disk);
            $images[$index] = $path;
        }

        // Ensure array is sequential
        $images = array_values($images);

        // Update model with JSON
        $model->update([$column => json_encode($images)]);

        return $images;
    }

    public function deleteAllMedia($model)
    {
        // Delete all images
        foreach ($model->images as $image) {
            $this->delete($image->path);
            $image->delete();
        }

        // Delete video if exists
        if ($model->video) {
            $this->delete($model->video);
        }
    }
}
