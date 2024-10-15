<?php

namespace Auth;

use database\Database;

class UserRoleManager
{
    private $conn;

    public function __construct(Database $db)
    {
        $this->conn = $db->getConnection();
    }

    // Function to revoke the role of a user (e.g., change to 'customer')
    public function revokeUserRole($user_uuid)
    {
        // Define the default role after revocation (e.g., 'customer')
        $default_role = 'customer'; 

        // Prepare SQL query to update the user's role
        $stmt = $this->conn->prepare("UPDATE users SET role = ?, updated_at = CURRENT_TIMESTAMP WHERE user_uuid = ?");
        $stmt->bind_param('ss', $default_role, $user_uuid);

        if ($stmt->execute()) {
            return ['message' => 'User role revoked successfully'];
        } else {
            return ['error' => 'Failed to revoke user role'];
        }
    }
}
