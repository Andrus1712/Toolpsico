<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identificacion', 'name', 'email', 'password',
    ];

    public static $rules = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function asignarRol($role)
    {
        $this->roles()->sync($role, false);
    }

    public function tieneRol()
    {
        return $this->roles->flatten()->pluck('slug')->unique();
    }

    public function nombreRol()
    {
        return $this->roles->flatten()->pluck('name')->unique();
    }

    public function descripcionRol()
    {
        return $this->roles->flatten()->pluck('descripcion')->unique();
    }


    public function hasRole($role)
    {
        if ($this->roles()->where('slug', $role)->first()) {
            return true;
        }
        return false;
    }

    public function havePermission($permission)
    {
        foreach ($this->roles as $role) {

            foreach ($role->permissions as $perm) {

                if ($perm->slug == $permission) {
                    return true;
                }
            }
        }

        return false;
        //return $this->roles;
    }
}
