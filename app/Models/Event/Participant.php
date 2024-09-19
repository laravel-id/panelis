<?php

namespace App\Models\Event;

use App\Enums\Participants\BloodType;
use App\Enums\Participants\Gender;
use App\Enums\Participants\IdentityType;
use App\Enums\Participants\Relation;
use App\Enums\Participants\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string $ulid
 * @property Status $status
 * @property BelongsTo $user
 */
class Participant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'package_id',
        'payment_id',
        'ulid',
        'bib',
        'id_type',
        'id_number',
        'name',
        'birthdate',
        'gender',
        'blood_type',
        'phone',
        'email',
        'emergency_name',
        'emergency_phone',
        'emergency_relation',
        'status',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'id_type' => IdentityType::class,
        'gender' => Gender::class,
        'blood_type' => BloodType::class,
        'emergency_relation' => Relation::class,
        'status' => Status::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $participant) {
            $participant->ulid = Str::ulid();
            $participant->status = Status::Pending;
        });
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Schedule>
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * @return BelongsTo<Package>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * @return BelongsTo<Payment>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
