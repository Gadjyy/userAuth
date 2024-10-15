<?php

namespace Api\Controllers;

use Auth\UserRoleManager;

class UserRoleController
{
    private $roleManager;

    public function __construct($db)
    {
        $this->roleManager = new UserRoleManager($db);
    }

    // Revoke user role
    public function revokeUserRole($data)
    {
        if (!empty($data['user_uuid'])) {
            return $this->roleManager->revokeUserRole($data['user_uuid']);
        } else {
            return ['error' => 'User UUID is required'];
        }
    }
}
