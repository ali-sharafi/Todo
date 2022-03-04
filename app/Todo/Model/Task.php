<?php

namespace Todo\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    protected $guarded = ['id'];

    /**
     * Each task has many labels
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwned($query)
    {
        return $query->where('user_id', auth()->user()->id);
    }
}
