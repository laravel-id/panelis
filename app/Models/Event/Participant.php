<?php

namespace App\Models\Event;

use App\Enums\Participants\BloodType;
use App\Enums\Participants\Gender;
use App\Enums\Participants\IdentityType;
use App\Enums\Participants\Relation;
use App\Enums\Participants\Status;
use App\Models\Traits\HasLocalTime;
use App\Models\Transaction\Transaction;
use App\Models\User;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $schedule_id
 * @property int $package_id
 * @property null|int $user_id
 * @property string $ulid
 * @property string $name
 * @property string $bib
 * @property Status $status
 * @property BelongsTo $user
 * @property BelongsTo $schedule
 */
class Participant extends Model implements HasLocalePreference
{
    use HasFactory;
    use HasLocalTime;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'package_id',
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
        'id_number' => 'encrypted',
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

    public function name(): Attribute
    {
        return new Attribute(
            set: function (string $name): string {
                return Str::of($name)
                    ->transliterate()
                    ->squish()
                    ->toString();
            },
        );
    }

    public function whatsapp(): Attribute
    {
        return new Attribute(
            get: function (): string {
                $phone = Str::of($this->phone);
                if ($phone->startsWith('+')) {
                    return $phone->replace('+', '');
                }

                if ($phone->startsWith('0')) {
                    return $phone->replaceStart('0', '62');
                }

                return $phone;
            },
        );
    }

    public function scopeIsFulfilled(Builder $query): void
    {
        $query->whereIn('status', [
            Status::Paid,
            Status::Completed,
            Status::Finished,
        ]);
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
     * @return HasMany<Participant>
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * @return MorphOne<Transaction>
     */
    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }

    public function preferredLocale(): string
    {
        return config('app.locale', 'en');
    }
}
