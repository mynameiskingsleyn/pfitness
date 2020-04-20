<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

Trait StorageTrait
{

    public function getFileFullPath()
    {
        if($this->disk){
            $storagePath = Storage::disk($this->disk)
                                    ->getDriver()
                                    ->getAdapter()
                                    ->getPathPrefix();
        }else{
            $storagePath =  Storage::disk()
                ->getDriver()
                ->getAdapter()
                ->getPathPrefix();
        }
        return $storagePath.'/'.$this->fileName;

    }
}