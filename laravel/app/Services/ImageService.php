<?php
namespace App\Services;

class ImageService{
    public function save($image,$location){
        return $image->store($location);
    }
}