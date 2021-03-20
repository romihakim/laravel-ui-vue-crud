<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'published',
    ];

    static function unique_slug($slug, $id = 0)
    {
        $count = self::where('id', '!=', $id)->where('slug', $slug)->count();

        if ($count > 0) {
            $exist = self::selectRaw('slug, CEIL(RIGHT(slug, (LENGTH(slug)-LENGTH("' . $slug . '")))) AS latest')
                ->where('slug', 'LIKE', $slug . '%')
                ->whereRaw('CEIL(RIGHT(slug, (LENGTH(slug)-LENGTH("' . $slug . '")))) <= 0')
                ->orderByRaw('CEIL(RIGHT(slug, (LENGTH(slug)-LENGTH("' . $slug . '")))) ASC')
                ->first();

            if (isset($exist)) {
                $last = intval('0' . Str::of($exist['latest'])->ltrim('-')) + 1;
                $slug = $slug . '-' . $last;
            } else {
                $slug = $slug . '-' . $count;
            }
        }

        return $slug;
    }
}
