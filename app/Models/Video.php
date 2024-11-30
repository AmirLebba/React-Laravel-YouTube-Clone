<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'path'];

    // Include quality URLs in the serialized model
    protected $appends = ['quality_urls'];

    // Accessor for quality URLs
    public function getQualityUrlsAttribute()
    {
        $baseFilename = pathinfo($this->path, PATHINFO_FILENAME);
        $resolutions = ['720p', '480p', '360p'];
        $urls = [];

        foreach ($resolutions as $resolution) {
            $filename = "videos/{$baseFilename}-{$resolution}.mp4";
            if (Storage::exists($filename)) {
                $urls[$resolution] = Storage::url($filename);
            }
        }

        return $urls;
    }
}