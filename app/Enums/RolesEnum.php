<?php

namespace App\Enums;

enum RolesEnum: string
{
        // case NAMEINAPP = 'name-in-database';

    case USER = 'user';
    case ADMIN = 'admin';
    case SUPERADMIN = 'super-admin';
    case ROOT = 'root';

    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
    public function label(): string
    {
        return match ($this) {
            static::USER => 'Users',
            static::ADMIN => 'Admins',
            static::SUPERADMIN => 'Super Admins',
            static::ROOT => 'Super Super Admins',
        };
    }

    public function description(): string
    {
        return match ($this) {
            static::USER => 'This is the customer user role.',
            static::ADMIN => 'This is the simple admin role.',
            static::SUPERADMIN => 'This is the main admin.',
            static::ROOT => 'This is the root role.',
        };
    }
}
