<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public static function get(string $key, $default = null)
    {
        $row = static::find($key);
        return ($row && $row->value !== null && $row->value !== '') ? $row->value : $default;
    }

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** Data-URI gambar pengaturan (aman untuk dompdf). */
    public static function img(string $key): ?string
    {
        $f = static::get($key);
        if (!$f || !Storage::disk('public')->exists('pengaturan/'.$f)) return null;
        $path = Storage::disk('public')->path('pengaturan/'.$f);
        return 'data:'.mime_content_type($path).';base64,'.base64_encode(file_get_contents($path));
    }

    public static function url(string $key): ?string
    {
        $f = static::get($key);
        return $f ? Storage::url('pengaturan/'.$f) : null;
    }
}
