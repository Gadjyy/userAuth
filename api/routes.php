<?php

require '../Auth/User.php';
require '../Auth/PasswordReset.php';
require './controllers/AuthController.php';
require './controllers/PasswordResetController.php';
require '../Auth/GetUserProfile.php';
require '../Auth/RevokeRole.php';
require './controllers/GetUserProfileController.php';
require './controllers/RevokeRoleController.php';

use Api\Controllers\AuthController;
use Api\Controllers\PasswordResetController;
use Api\Controllers\UserProfileController;
use Api\Controllers\RevokeRoleController;

class Router
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function route($action, $data)
    {
        switch ($action) {
            case 'register':
                return $this->handleRegister($data);
            case 'login':
                return $this->handleLogin($data);
            case 'verify_code':
                return $this->handleVerifyCode($data);
            case 'logout':
                return $this->handleLogout();
            case 'reset_request':
                return $this->handleResetRequest($data);
            case 'reset_password':
                return $this->handleResetPassword($data);
            case 'get_user_profile':
                return $this->handleGetUserProfile($data);
            case 'update_user_profile':
                return $this->handleUpdateUserProfile($data);
            case 'update_user_email':
                return $this->handleUpdateUserEmail($data);
            default:
                return ['error' => 'Action not found'];
        }
    }

    private function handleRegister($data)
    {
        $controller = new AuthController($this->db);
        return $controller->register($data);
    }

    private function handleLogin($data)
    {
        $controller = new AuthController($this->db);
        return $controller->login($data);
    }

    private function handleVerifyCode($data)
    {
        $controller = new AuthController($this->db);
        return $controller->verifyCode($data);
    }

    private function handleLogout()
    {
        $controller = new AuthController($this->db);
        return $controller->logout();
    }

    private function handleResetRequest($data)
    {
        $controller = new PasswordResetController($this->db);
        return $controller->requestPasswordReset($data);
    }

    private function handleResetPassword($data)
    {
        $controller = new PasswordResetController($this->db);
        return $controller->resetPassword($data);
    }
    private function handleGetUserProfile()
    {
        $controller = new UserProfileController($this->db);
        return $controller->getUserProfile();
    }
    private function handleUpdateUserProfile($data)
    {
        $controller = new UserProfileController($this->db);
        return $controller->updateUserProfile($data);
    }
    private function handleUpdateUserEmail($data)
    {
        $controller = new UserProfileController($this->db);
        return $controller->updateUserEmail($data);
    }
}
