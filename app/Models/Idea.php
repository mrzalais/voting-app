<?php

namespace App\Models;

use Database\Factories\IdeaFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Idea
 *
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property int $status_id
 * @property string $title
 * @property string|null $slug
 * @property string $description
 * @property int $spam_reports
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $voted_by_user
 * @property-read User $User
 * @property-read Category $category
 * @property-read Status $status
 * @property-read Collection|User[] $votes
 * @property-read int|null $votes_count
 * @method static IdeaFactory factory(...$parameters)
 * @method static Builder|Idea findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder|Idea newModelQuery()
 * @method static Builder|Idea newQuery()
 * @method static Builder|Idea query()
 * @method static Builder|Idea whereCategoryId($value)
 * @method static Builder|Idea whereCreatedAt($value)
 * @method static Builder|Idea whereDescription($value)
 * @method static Builder|Idea whereId($value)
 * @method static Builder|Idea whereSlug($value)
 * @method static Builder|Idea whereSpamReports($value)
 * @method static Builder|Idea whereStatusId($value)
 * @method static Builder|Idea whereTitle($value)
 * @method static Builder|Idea whereUpdatedAt($value)
 * @method static Builder|Idea whereUserId($value)
 * @method static Builder|Idea withUniqueSlugConstraints(Model $model, string $attribute, array $config, string $slug)
 * @mixin Eloquent
 * @property-read Collection|Comment[] $comments
 * @property-read int|null $comments_count
 */
class Idea extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = [];
    protected $perPage = 10;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'votes');
    }

    public function isVotedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return Vote::where('user_id', $user->id)
            ->where('idea_id', $this->id)
            ->exists();
    }

    public function vote(?User $user): void
    {
        $this->votes()->attach($user);
    }

    public function removeVote(?User $user): void
    {
        $this->votes()->detach($user);
    }
}
