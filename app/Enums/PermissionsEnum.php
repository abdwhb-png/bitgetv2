<?php

namespace App\Enums;

enum PermissionsEnum: string
{
        // case NAMEINAPP = 'name-in-database';

    case INVITEUSERS = 'invite-user';

    case INVITEADMINS = 'invite-admin';

    case EDITROLES = 'edit-role';

    case EDITPERMISSIONS = 'edit-permission';

    case EDITUSERS = 'edit-user';

    case EDITORDERS = 'edit-order';

    case EDITTRANSACTIONS = 'edit-transaction';

    case EDITVERIFICATIONS = 'edit-verification';

    case MANAGEADMIN = 'manage-admin';

    case DELETEUSERS = 'delete-user';
}