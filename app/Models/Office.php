<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Screen\AsSource;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $timezone
 * @property string|null $location
 * @property string|null $description
 * @property Carbon start_time
 * @property Carbon end_time
 *
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static \Database\Factories\OfficeFactory factory($count = null, $state = [])
 * @method static Builder<static>|Office newModelQuery()
 * @method static Builder<static>|Office newQuery()
 * @method static Builder<static>|Office query()
 * @method static Builder<static>|Office whereCreatedAt($value)
 * @method static Builder<static>|Office whereDescription($value)
 * @method static Builder<static>|Office whereId($value)
 * @method static Builder<static>|Office whereLocation($value)
 * @method static Builder<static>|Office whereName($value)
 * @method static Builder<static>|Office whereTimezone($value)
 * @method static Builder<static>|Office whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Office extends Model
{
    /** @use HasFactory<\Database\Factories\OfficeFactory> */
    use HasFactory;
    use SoftDeletes;
    use AsSource;
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];
    protected $fillable = [
        'name', 'start_time', 'end_time', 'timezone', 'location', 'description'
    ];

    public function rooms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Room::class);
    }
}
