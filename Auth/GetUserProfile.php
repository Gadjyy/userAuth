<?php

namespace Auth;

use database\Database;

class UserProfile
{
    private $conn;

    public function __construct(Database $db)
    {
        $this->conn = $db->getConnection();
    }

    // Function to get the user profile
    public function getUserProfile($user_uuid)
    {
        // Prepare SQL to retrieve user profile
        $stmt = $this->conn->prepare("SELECT user_uuid, first_name, last_name, email, address, phone_number, role, created_at, updated_at
                                      FROM users
                                      WHERE user_uuid = ?");
        $stmt->bind_param('s', $user_uuid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        } else {
            return ['error' => 'User not found'];
        }
    }

    // Function to update user profile
    public function updateUserProfile($user_uuid, $data)
    {
        // Extract fields to update
        $first_name = $data['first_name'] ?? null;
        $last_name = $data['last_name'] ?? null;
        $address = $data['address'] ?? null;
        $phone_number = $data['phone_number'] ?? null;

        // Prepare SQL query to update user profile
        $stmt = $this->conn->prepare("UPDATE users SET 
                                        first_name = ?, 
                                        last_name = ?, 
                                        address = ?, 
                                        phone_number = ?,
                                        updated_at = CURRENT_TIMESTAMP
                                      WHERE user_uuid = ?");

        $stmt->bind_param('sssss', $first_name, $last_name, $address, $phone_number, $user_uuid);

        if ($stmt->execute()) {
            return ['message' => 'User profile updated successfully'];
        } else {
            return ['error' => 'Failed to update user profile'];
        }
    }

    // Example function to update user's email
    public function updateUserEmail($user_uuid, $new_email)
    {
        // Check if the email is already in use
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param('s', $new_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Email is not in use, proceed with updating the email
            $stmt = $this->conn->prepare("UPDATE users SET email = ?, updated_at = CURRENT_TIMESTAMP WHERE user_uuid = ?");
            $stmt->bind_param('ss', $new_email, $user_uuid);

            if ($stmt->execute()) {
                return ['message' => 'Email updated successfully'];
            } else {
                return ['error' => 'Failed to update email'];
            }
        } else {
            return ['error' => 'Email already in use'];
        }
    }
}
