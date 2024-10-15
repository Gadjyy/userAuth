<?php

namespace Auth;

use database\Database;

class UserRoles
{
    public $conn;

    public function __construct(Database $db)
    {
        $this->conn = $db->getConnection();
    }

    // Function to list roles assigned to a specific user (optional helper if needed)
    public function getRolesByUserId($user_id)
    {
        $stmt = $this->conn->prepare("
            SELECT r.name AS role_name
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ?
        ");
        $stmt->bind_param('s', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row['role_name'];
        }

        return $roles;
    }
}
