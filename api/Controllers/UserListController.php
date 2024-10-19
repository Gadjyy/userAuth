<?php

namespace Api\Controllers;

use Auth\UserList;

class UserListController
{
    private $userList;

    public function __construct($db)
    {
        $this->userList = new UserList($db);
    }

    public function getUserRoles($data)
    {
        $headers = getallheaders();

        if (empty($headers['Authorization']) || !preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return ['error' => 'Authorization token not provided or invalid'];
        }

        $admin_token = $matches[1];


        return $this->userList->listAllUsersWithRoles($admin_token);
    }
}
