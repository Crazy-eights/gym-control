<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class VisualConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'secondary_logo',
        'primary_color',
        'secondary_color',
        'accent_color',
        'navbar_color',
        'sidebar_color',
        'font_family',
        'favicon',
        'meta_description',
        'custom_css'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the visual configuration with caching
     */
    public static function getConfig()
    {
        return Cache::remember('visual_config', 3600, function () {
            return self::first();
        });
    }

    /**
     * Clear the cache when model is saved or deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('visual_config');
        });

        static::deleted(function () {
            Cache::forget('visual_config');
        });
    }

    /**
     * Get color scheme as array
     */
    public function getColorScheme()
    {
        return [
            'primary' => $this->primary_color,
            'secondary' => $this->secondary_color,
            'accent' => $this->accent_color,
            'navbar' => $this->navbar_color,
            'sidebar' => $this->sidebar_color,
        ];
    }

    /**
     * Check if logo exists
     */
    public function hasLogo()
    {
        return !empty($this->logo);
    }

    /**
     * Check if secondary logo exists
     */
    public function hasSecondaryLogo()
    {
        return !empty($this->secondary_logo);
    }

    /**
     * Get logo URL
     */
    public function getLogoUrl()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    /**
     * Get secondary logo URL
     */
    public function getSecondaryLogoUrl()
    {
        return $this->secondary_logo ? asset('storage/' . $this->secondary_logo) : null;
    }
}