<?php

namespace App\Models;

use App\Enums\TokenTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * App\Models\Token
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property TokenTypeEnum $type
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token query()
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUserId($value)
 * @mixin \Eloquent
 */
class Token extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'token',
        'expires_at',
        'type',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'type' => TokenTypeEnum::class,
    ];

    public function encrypt(): string
    {
        return Crypt::encrypt([$this->user_id, $this->token]);
    }
}
