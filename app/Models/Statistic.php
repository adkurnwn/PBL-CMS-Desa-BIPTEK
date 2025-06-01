<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Statistic extends Model
{
    protected $fillable = [
        'type',
        'category',
        'content_id',
        'feature_name',
        'ip_hash',
        'user_agent_hash',
        'session_id',
        'date',
        'time',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i:s',
    ];

    // Relationships for content lookups
    public function news()
    {
        return $this->belongsTo(News::class, 'content_id')->when($this->category === 'news');
    }

    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'content_id')->when($this->category === 'gallery');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'content_id')->when($this->category === 'event');
    }

    public function produkHukum()
    {
        return $this->belongsTo(ProdukHukum::class, 'content_id')->when($this->category === 'produk_hukum');
    }

    public function danaDesa()
    {
        return $this->belongsTo(DanaDesa::class, 'content_id')->when($this->category === 'dana_desa');
    }

    public function produkDesa()
    {
        return $this->belongsTo(ProdukDesa::class, 'content_id')->when($this->category === 'produk_desa');
    }

    // Scopes for filtering
    public function scopeContentViews($query)
    {
        return $query->where('type', 'content_view');
    }

    public function scopeFeatureVisits($query)
    {
        return $query->where('type', 'feature_visit');
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('date', Carbon::now()->year);
    }
}
