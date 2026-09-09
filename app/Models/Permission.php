<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    /**
     * The main-module permission this one is nested under (via `parent_id`),
     * used to group modules -> sub-modules -> actions in the permissions UI.
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * The permissions nested directly under this one.
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
