<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Todo",
 *     type="object",
 *     title="Todo",
 *     description="Todo model",
 *     @OA\Property(
 *         property="uuid",
 *         type="string",
 *         format="uuid",
 *         description="Unique identifier for the todo"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the todo"
 *     ),
 *     @OA\Property(
 *         property="priority",
 *         type="string",
 *         description="Priority level of the todo",
 *         enum={"low", "medium", "high", "highest"},
 *         default="medium"
 *     ),
 *     @OA\Property(
 *         property="due_date",
 *         type="string",
 *         format="date-time",
 *         description="Due date of the todo"
 *     ),
 *     @OA\Property(
 *         property="completed_at",
 *         type="string",
 *         format="date-time",
 *         nullable=true,
 *         description="Completion date of the todo"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last updated timestamp"
 *     )
 * )
 */
class Todo extends Model
{
    /** @use HasFactory<\Database\Factories\TodoFactory> */
    use HasFactory;

    // Add the primary key configuration here
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'description',
        'priority',
        'due_date',
        'completed_at',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }
}
