<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Database\Factories\StatusFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Status
 *
 * @property int $id
 * @property string $name
 * @property string $class
 * @property string|null $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Idea[] $ideas
 * @property-read int|null $ideas_count
 * @method static StatusFactory factory(...$parameters)
 * @method static Builder|Status findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static Builder|Status newModelQuery()
 * @method static Builder|Status newQuery()
 * @method static Builder|Status query()
 * @method static Builder|Status whereClass($value)
 * @method static Builder|Status whereCreatedAt($value)
 * @method static Builder|Status whereId($value)
 * @method static Builder|Status whereName($value)
 * @method static Builder|Status whereSlug($value)
 * @method static Builder|Status whereUpdatedAt($value)
 * @method static Builder|Status withUniqueSlugConstraints(Model $model, string $attribute, array $config, string $slug)
 * @mixin Eloquent
 */
class Status extends Model
{
    use HasFactory, Sluggable;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class);
    }

    public static function getCount(): array
    {
        return Idea::query()
            ->selectRaw('count(*) as all_statuses')
            ->selectRaw('count(case when status_id = 1 then 1 end) as open')
            ->selectRaw('count(case when status_id = 2 then 2 end) as considering')
            ->selectRaw('count(case when status_id = 3 then 3 end) as in_progress')
            ->selectRaw('count(case when status_id = 4 then 4 end) as implemented')
            ->selectRaw('count(case when status_id = 5 then 5 end) as closed')
            ->first()
            ->toArray();
    }

    public function getStatusClass(): string
    {
        $allStatuses = [
            'Open' => 'bg-gray-200',
            'Considering' => 'bg-purple text-white',
            'In Progress' => 'bg-yellow text-white',
            'Implemented' => 'bg-green text-white',
            'Closed' => 'bg-red text-white',
        ];

        return $allStatuses[$this->name];
    }
}
