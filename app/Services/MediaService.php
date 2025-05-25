<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use App\Models\Image as ImageModel;

class MediaService
{
    public function upload($file, $path, $disk = 'public')
    {
        $filename = uniqid() . '.webp';
        $fullPath = $path . '/' . $filename;

        if (str_starts_with($file->getMimeType(), 'image/')) {
            // Convert and encode image to WebP
            $image = Image::make($file)
                ->resize(1600, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80);

            // Save the WebP file to storage
            Storage::disk($disk)->put($fullPath, (string) $image);

            // Optimize the image file (optional, works with WebP too)
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
        // Determine the appropriate storage path based on model type
        if (!$path) {
            $className = class_basename($model);
            $path = strtolower($className) . '-images';
        }

        $createdImages = [];
        $defaultAltText = $model->title ?? $model->name ?? 'Image';

        foreach ($images as $index => $image) {
            $filePath = $this->upload($image, $path, $disk);

            // Get alt text or use default
            $altText = isset($alts[$index]) && !empty(trim($alts[$index]))
                ? $alts[$index]
                : $defaultAltText;

            // Create the image record using the appropriate relationship
            $imageData = [
                'path' => $filePath,
                'alt' => $altText,
                'is_visible' => true
            ];

            // Different models have different relationship methods
            if (method_exists($model, 'images')) {
                $createdImage = $model->images()->create($imageData);
                // Return data in format expected by controller
                $createdImages[] = [
                    'path' => $filePath,
                    'alt' => $altText,
                    'id' => $createdImage->id
                ];
            } elseif (method_exists($model, 'gambarProduk')) {
                $createdImage = $model->gambarProduk()->create($imageData);
                $createdImages[] = [
                    'path' => $filePath,
                    'alt' => $altText,
                    'id' => $createdImage->id
                ];
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

        // Generate unique filename with proper extension
        $extension = $video->getClientOriginalExtension();
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $fullPath = $path . '/' . $filename;

        // Store the video file
        $video->storeAs($path, $filename, $disk);

        // Update model with new path
        $model->update([$column => $fullPath]);

        return $fullPath;
    }

    public function handleVideoRemoval($model, $column = 'video', $disk = 'public')
    {
        if (!empty($model->$column)) {
            $this->delete($model->$column, $disk);
            $model->update([$column => null]);
            return true;
        }
        return false;
    }

    public function deleteAllMedia($model)
    {
        // Delete all images
        if (method_exists($model, 'images')) {
            foreach ($model->images as $image) {
                $this->delete($image->path);
                $image->delete();
            }
        }

        // Delete video if exists
        if (!empty($model->video)) {
            $this->delete($model->video);
        }
    }

    public function handleImageAltTextUpdates($model, $updatedImages)
    {
        $count = 0;
        $defaultAltText = $model->title ?? $model->name ?? 'Image';

        foreach ($updatedImages as $updatedImage) {
            if (isset($updatedImage['id'])) {
                // Find the image using the relationship on the model
                $image = $model->images()->find($updatedImage['id']);

                if ($image) {
                    // Use the alt text if provided, otherwise use the default title
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
}
