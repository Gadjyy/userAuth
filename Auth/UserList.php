<?php

namespace Auth;

use Auth\UserAssign;

class UserList
{
    private $conn;
    private $validateToken;

    public function __construct($db)
    {
        $this->conn = $db->getConnection();
        $this->validateToken = new UserAssign($db);
    }

    // Function to list all users and their roles
    public function listAllUsersWithRoles($admin_token)
    {
        $admin_uuid = $this->validateToken->validateToken($admin_token);

        if ($admin_uuid === null) {
            return ['error' => 'Invalid token or token has expired'];
        }

        // Check if the admin user has admin role
        $stmt = $this->conn->prepare("SELECT role FROM users WHERE user_uuid = ? AND role = 'admin'");
        $stmt->bind_param('s', $admin_uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['error' => 'You do not have permission to revoke roles'];
        }

        $stmt = $this->conn->prepare("
        SELECT user_uuid, role
        FROM users
    ");

        $stmt->execute();
        $result = $stmt->get_result();

        // Initialize an array to hold user data
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                'user_id' => $row['user_uuid'],
                'role' => $row['role']
            ];
        }

        return $users;
    }
}
