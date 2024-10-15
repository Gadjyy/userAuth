<?php

namespace Api\Controllers;

use Auth\UserRoles;

class UserRolesController
{
    public $userRole;

    public function __construct($db)
    {
        $this->userRole = new UserRoles($db);
    }

    // Function to extract bearer token from Authorization header
    private function getBearerToken()
    {
        $headers = getallheaders();

        if (isset($headers['Authorization'])) {
            // Authorization: Bearer <token>
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    // Function to list roles of a user
    public function listUserRoles($user_id)
    {
        // Get the bearer token from the Authorization header
        $token = $this->getBearerToken();

        if ($token) {
            // Validate the token and ensure it's an admin token
            $stmt = $this->userRole->conn->prepare("SELECT user_uuid FROM session_token WHERE token = ? AND role = 'admin' AND expires_at > NOW()");
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                // Token is valid, and user has admin privileges
                // Fetch roles assigned to the user
                $stmt_roles = $this->userRole->conn->prepare("SELECT role FROM user_roles WHERE user_id = ?");
                $stmt_roles->bind_param('s', $user_id);
                $stmt_roles->execute();
                $result_roles = $stmt_roles->get_result();

                $roles = [];
                while ($row = $result_roles->fetch_assoc()) {
                    $roles[] = $row['role'];
                }

                if (!empty($roles)) {
                    return [
                        'user_id' => $user_id,
                        'roles' => $roles
                    ];
                } else {
                    return [
                        'user_id' => $user_id,
                        'roles' => []
                    ];
                }
            } else {
                return ['error' => 'Invalid or expired admin token'];
            }
        } else {
            return ['error' => 'Authorization token not found'];
        }
    }
}
