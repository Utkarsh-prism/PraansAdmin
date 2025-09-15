<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HolidayDetail extends Model
{
    protected $fillable = [
        'holiday_name','type','date','day','month', 
        // 'holiday_pdf',
        'sort_order'  
    ];

    public function holiday(): BelongsTo
    {
        return $this->belongsTo(Holiday::class);
    }
}
