<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

use Illuminate\Support\Facades\Session;

use App\Models\OrgStructureTree;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'position_id',
        'department_id',
        'name',
        'email',
        'password',
        'profile_pic',
        'dark_mode',
        'google_id',
        'last_activity',
        'signature',
        'cost_center',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Dynamically set the database connection based on the session.
     */
    public function getConnectionName()
    {
        return Session::get('db_connection', 'mysql'); // Default to 'mysql' if not set
    }

    public function isOnline() {
        return $this->last_activity >= now()->subMinutes(2);
    }

    public function adminlte_image()
    {
        // random image
        return !empty($this->profile_pic) ? asset($this->profile_pic) : asset('images/Default_pfp.svg.png');
    }

    public function adminlte_desc()
    {
        return implode(', ', $this->getRoleNames()->toArray()) ?? '-';
    }

    public function adminlte_profile_url()
    {
        return 'profile/'.encrypt($this->id);
    }

    public function adminlte_signature()
    {
        return !empty($this->signature) ? asset($this->signature) : asset('images/nosign.png');
    }

    public function company() {
        return $this->belongsTo('App\Models\Company');
    }

    public function department() {
        return $this->belongsTo('App\Models\Department');
    }

    public function position() {
        return $this->belongsTo('App\Models\Position');
    }
    /**
     * Get the org structure trees associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function structure_trees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrgStructureTree::class, 'user_id', 'id');
    }

    /**
     * Get all subordinate user IDs organized by reporting level.
     *
     * @return array<int, array<int, int>>
     */
    public function getSubordinateIds(): array
    {
        $subordinateIds = [];
        $structures = $this->structure_trees()->get();

        foreach ($structures as $structure) {
            $this->collectSubordinateIds($structure, $subordinateIds);
        }

        return $subordinateIds;
    }

    /**
     * Recursively collect subordinate user IDs up to a max depth.
     *
     * @param  \App\Models\OrgStructureTree  $structure
     * @param  array<int, array<int, int>>  &$subordinateIds
     * @param  int  $level
     * @return void
     */
    private function collectSubordinateIds(OrgStructureTree $structure, array &$subordinateIds, int $level = 1): void
    {
        if ($level > 5) {
            return;
        }

        if ($structure->user_id) {
            $subordinateIds['level_' . $level][] = $structure->user_id;
        }

        $children = OrgStructureTree::where('reports_to_id', $structure->id)->get();

        foreach ($children as $child) {
            $this->collectSubordinateIds($child, $subordinateIds, $level + 1);
        }
    }

    /**
     * Get all supervisor user IDs in the reporting chain.
     *
     * @return array<int>
     */
    public function getSupervisorIds(): array
    {
        $supervisorIds = [];
        $structure = $this->structure_trees()->first();

        while ($structure && $structure->reports_to_id) {
            $superior = OrgStructureTree::find($structure->reports_to_id);

            if ($superior && $superior->user_id) {
                $supervisorIds[] = $superior->user_id;
            }

            $structure = $superior;
        }

        return array_unique($supervisorIds);
    }

    /**
     * Get the immediate superior's user ID.
     *
     * @return int|null
     */
    public function getImmediateSuperiorId(): ?int
    {
        $structure = $this->structure_trees()->first();

        if ($structure && $structure->reports_to_id) {
            $superior = OrgStructureTree::find($structure->reports_to_id);

            return $superior?->user_id;
        }

        return null;
    }
}
