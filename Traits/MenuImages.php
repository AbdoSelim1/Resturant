<?php
namespace App\Traits;

use Intervention\Image\Facades\Image;

trait MenuImages
{
    public array $resizeableImages = [];
    public function storeImages(array $images): self
    {
        foreach ($images as $index => $image) {
            $this->addMedia($image['file_name'])->toMediaCollection('menues'); // store new image
            if (!empty($image['width']) && !empty($image['height'])) {
                $this->resizeableImages[] = ['index' => $index, 'width' => $image['width'], 'height' => $image['height']];
            }
        }
        return $this;
    }

    public function resize() :void
    {
        $menuPhotos = $this->getMedia('menues');
        foreach ($this->resizeableImages as $resizeableImage) {
            Image::make($menuPhotos[$resizeableImage['index']]->getPath())
                ->resize($resizeableImage['width'], $resizeableImage['height'])
                ->save($menuPhotos[$resizeableImage['index']]->getPath());
        }
    }
}
