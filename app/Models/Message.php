<?php

namespace App\Models;

use App\Filament\Resources\MessageResource\Enums\MessageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property MessageStatus $status
 * @property int $id
 * @property ?string $email
 */
class Message extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'body',
        'status',
    ];

    protected $attributes = [
        'status' => MessageStatus::Unread,
    ];

    protected $casts = [
        'status' => MessageStatus::class,
    ];

    public function markAsRead(): bool
    {
        $this->status = MessageStatus::Read;

        return $this->save();
    }

    public function markAsUnread(): bool
    {
        $this->status = MessageStatus::Unread;

        return $this->save();
    }

    public function markAsSpam(): bool
    {
        $this->status = MessageStatus::Spam;

        return $this->save();
    }
}
