<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActForm extends Model
{
    protected $fillable = ['form_no','title','short_desc','pdf_path','sort_order'];

    public function actRuleForm(): BelongsTo
    {
        return $this->belongsTo(ActRuleForm::class);
    }
}
