<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_name',
        'media_ext',
        'folder',
    ];

    private static array $resolutions = [
        'xs' => 100,
        'sm' => 300,
        'md' => 600,
        'lg' => 900,
        'xl' => 1200,
    ];

    public static function resolutions()
    {
        return self::$resolutions;
    }

    private static array $extensions = [
        "jpeg" => true,
        "jpg" => true,
        "png" => true,
        "webp" => true,
    ];

    public static function extensions()
    {
        return self::$extensions;
    }

    public function user(){
        return $this->hasOne(User::class);
    }

    public function getPath(string $size): string
    {
        return "assets/" . $this->folder . $size . '_' . $this->media_name . '.' . $this->media_ext;
    }
}
