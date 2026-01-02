<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../../validation/AuthValidation.php';

class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once __DIR__ . '/../views/login.php';
            return;
        }

        list($errors, $clean) = validateLogin($_POST);
        $old = ['login' => $clean['login']];

        if (!empty($errors)) {
            require_once __DIR__ . '/../views/login.php';
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmailOrPhone($clean['login']);

        if (!$user) {
            $errors[] = "User not found.";
            require_once __DIR__ . '/../views/login.php';
            return;
        }

        if (!password_verify($clean['password'], $user['password_hash'])) {
            $errors[] = "Wrong password.";
            require_once __DIR__ . '/../views/login.php';
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        if ($user['role'] === 'seller') {
            header("Location: index.php?url=household/dashboard");
            exit;
        }

        if ($user['role'] === 'buyer') {
            header("Location: index.php?url=dealer/dashboard");
            exit;
        }

        header("Location: index.php");
        exit;
    }

    public function register_seller()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once __DIR__ . '/../views/register_seller.php';
            return;
        }
        list($errors, $clean) = validateSellerRegister($_POST);

        $old = [
            'name'  => $clean['name'],
            'area'  => $clean['area'],
            'phone' => $clean['phone'],
            'email' => $clean['email']
        ];

        if (!empty($errors)) {
            require_once __DIR__ . '/../views/register_seller.php';
            return;
        }

        $userModel = new UserModel();

        if ($userModel->existsByEmailOrPhone($clean['email'], $clean['phone'])) {
            $errors[] = "Email or phone already registered.";
            require_once __DIR__ . '/../views/register_seller.php';
            return;
        }

        $hash = password_hash($clean['password'], PASSWORD_DEFAULT);

        $userModel->createSeller(
            $clean['name'],
            $clean['area'],
            $clean['phone'],
            $clean['email'],
            $hash
        );

        header("Location: index.php?url=auth/login");
        exit;
    }

    public function register_buyer()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once __DIR__ . '/../views/register_buyer.php';
            return;
        }

        list($errors, $clean) = validateBuyerRegister($_POST);

        $old = [
            'owner_name' => $clean['owner_name'],
            'shop_name'  => $clean['shop_name'],
            'area'       => $clean['area'],
            'phone'      => $clean['phone'],
            'email'      => $clean['email']
        ];

        if (!empty($errors)) {
            require_once __DIR__ . '/../views/register_buyer.php';
            return;
        }

        $userModel = new UserModel();

        if ($userModel->existsByEmailOrPhone($clean['email'], $clean['phone'])) {
            $errors[] = "Email or phone already registered.";
            require_once __DIR__ . '/../views/register_buyer.php';
            return;
        }

        $hash = password_hash($clean['password'], PASSWORD_DEFAULT);

        $userModel->createBuyer(
            $clean['owner_name'],
            $clean['shop_name'],
            $clean['area'],
            $clean['phone'],
            $clean['email'],
            $hash
        );

        header("Location: index.php?url=auth/login");
        exit;
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
