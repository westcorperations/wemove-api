<?php

use App\Models\Roles;

if (! function_exists('has_role')) {
    function has_role($role) {
        $user = auth()->user()->id;
        $roles = Roles::where('user_id', $user)->where('role',$role)->exists();
        if ($user && $roles) {
            return true;
        }
        return false;
    }
}
