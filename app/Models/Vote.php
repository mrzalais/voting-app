<?php

namespace App\Models;

use Database\Factories\VoteFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Vote
 *
 * @property int $id
 * @property int $idea_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static VoteFactory factory(...$parameters)
 * @method static Builder|Vote newModelQuery()
 * @method static Builder|Vote newQuery()
 * @method static Builder|Vote query()
 * @method static Builder|Vote whereCreatedAt($value)
 * @method static Builder|Vote whereId($value)
 * @method static Builder|Vote whereIdeaId($value)
 * @method static Builder|Vote whereUpdatedAt($value)
 * @method static Builder|Vote whereUserId($value)
 * @mixin Eloquent
 */
class Vote extends Model
{
    use HasFactory;

    protected $guarded = [];
}
