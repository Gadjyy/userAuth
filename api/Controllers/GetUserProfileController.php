<?php

namespace Api\Controllers;

use Auth\UserProfile;

class UserProfileController
{
    public $userProfile;

    public function __construct($db)
    {
        $this->userProfile = new UserProfile($db);
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

    // Function to get user profile by validating the token
    public function getUserProfile()
    {
        // Get the bearer token from the Authorization header
        $token = $this->getBearerToken();

        if ($token) {
            // Validate the token and get user_uuid from session_token table
            $stmt = $this->userProfile->conn->prepare("SELECT user_uuid FROM session_token WHERE token = ? AND expires_at > NOW()");
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                // Token is valid, fetch user profile
                $session = $result->fetch_assoc();
                $user_uuid = $session['user_uuid'];

                return $this->userProfile->getUserProfile($user_uuid);
            } else {
                return ['error' => 'Invalid or expired token'];
            }
        } else {
            return ['error' => 'Authorization token not found'];
        }
    }
}
