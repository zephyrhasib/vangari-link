<?php

require_once __DIR__ . '/../models/HouseholdAccountModel.php';
require_once __DIR__ . '/../../validation/AccountValidation.php';
require_once __DIR__ . '/../models/PriceModel.php';
require_once __DIR__ . '/../../validation/PickupRequestValidation.php';
require_once __DIR__ . '/../models/PickupRequestModel.php';

class HouseholdController
{
    private function requireSeller()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'seller') {
            header("Location: index.php?url=dealer/dashboard");
            exit;
        }
    }
    
    
    public function dashboard()
    {
        
        $this->requireSeller();
        require_once __DIR__ . '/../views/dashboard.php';
    }
    public function account()
    {
        $this->requireSeller();

        $model = new HouseholdAccountModel();
        $user = $model->findSellerById((int)$_SESSION['user_id']);

        if (!$user) {
            header("Location: index.php?url=auth/logout");
            exit;
        }

        $flash = $_SESSION['flash'] ?? '';
        unset($_SESSION['flash']);

        require_once __DIR__ . '/../views/account.php';
    }
        
    public function update_profile()
    {
        $this->requireSeller();

        list($errors, $clean) = validateSellerProfileUpdate($_POST);
        if (!empty($errors)) {
            header("Location: index.php?url=household/account");
            exit;
        }

        $model = new HouseholdAccountModel();
        $uid = (int)$_SESSION['user_id'];

        if ($model->emailOrPhoneTakenByOthers($uid, $clean['email'], $clean['phone'])) {
            header("Location: index.php?url=household/account");
            exit;
        }

        $model->updateSellerProfile(
            $uid,
            $clean['name'],
            $clean['area'],
            $clean['phone'],
            $clean['email']
        );

        $_SESSION['name'] = $clean['name'];
        $_SESSION['flash'] = "Profile updated successfully.";

        header("Location: index.php?url=household/account");
        exit;
    }

    public function change_password()
    {
        $this->requireSeller();

        list($errors, $clean) = validateSellerPasswordChange($_POST);
        if (!empty($errors)) {
            header("Location: index.php?url=household/account");
            exit;
        }

        $model = new HouseholdAccountModel();
        $uid = (int)$_SESSION['user_id'];

        if (!$model->verifySellerPassword($uid, $clean['current'])) {
            header("Location: index.php?url=household/account");
            exit;
        }

        if ($model->verifySellerPassword($uid, $clean['new'])) {
            header("Location: index.php?url=household/account");
            exit;
        }

        $hash = password_hash($clean['new'], PASSWORD_DEFAULT);
        $model->updateSellerPassword($uid, $hash);

        $_SESSION['flash'] = "Password changed successfully.";
        header("Location: index.php?url=household/account");
        exit;
    }

    public function delete_account()
    {
        $this->requireSeller();

        $confirmText = trim($_POST['confirm_delete'] ?? '');
        $password    = $_POST['delete_password'] ?? '';

        if ($confirmText !== 'YES') {
            header("Location: index.php?url=household/account");
            exit;
        }

        $model = new HouseholdAccountModel();
        $uid = (int)$_SESSION['user_id'];

        if (!$model->verifySellerPassword($uid, $password)) {
            header("Location: index.php?url=household/account");
            exit;
        }

        $ok = $model->deleteSellerAccount($uid);
        if (!$ok) {
            header("Location: index.php?url=household/account");
            exit;
        }

        header("Location: index.php?url=auth/logout");
        exit;
    }

    public function check_prices()
    {
        $this->requireSeller();
        $model = new PriceModel();
        $rows = $model->getTodayAndYesterdayPrices();
        require_once __DIR__ . '/../views/check_prices.php';
    }


     public function create_request()
    {
         $this->requireSeller();
      

        $model = new PickupRequestModel();

        $sellerId = (int)$_SESSION['user_id'];

        $items = $model->getActiveScrapItems();

        $info = $model->getSellerInfo($sellerId);
        $prefillPhone = $info['phone'] ?? '';
        $sellerArea   = $info['area'] ?? '';

        require_once __DIR__ . '/../views/create_request.php';
    }

    public function submit_request()
    {
        header('Content-Type: application/json; charset=utf-8');

         $this->requireSeller();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'errors' => ['Invalid request method.']]);
            exit;
        }

        list($errors, $clean) = validatePickupRequest($_POST);

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }

        $sellerId = (int)$_SESSION['user_id'];

        $model = new PickupRequestModel();
        $sellerArea = $model->getSellerArea($sellerId);

        if (!$sellerArea) {
            echo json_encode(['success' => false, 'errors' => ['Your area is missing. Update your profile area first.']]);
            exit;
        }

        $desiredMysql = str_replace('T', ' ', $clean['desired_datetime']) . ':00';

        $ok = $model->create(
        $sellerId,
        $sellerArea,
        (int)$clean['scrap_item_id'],
        (float)$clean['estimated_weight'],
        $clean['contact_phone'],
        $clean['address'],
        $desiredMysql
        );


        if (!$ok) {
            echo json_encode(['success' => false, 'errors' => ['Failed to submit request. Try again.']]);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Pickup request submitted successfully.']);
        exit;
    }


    public function order_tracking()
    {
        $this->requireSeller();
        require_once __DIR__ . '/../views/order_tracking.php';
    }



    public function order_tracking_data()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'errors' => ['Please login first.']]);
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'seller') {
            echo json_encode(['success' => false, 'errors' => ['Access denied.']]);
            exit;
        }

        $model = new PickupRequestModel();

        $sellerId = (int)$_SESSION['user_id'];
        $rows = $model->getSellerRequests($sellerId);

        echo json_encode(['success' => true, 'rows' => $rows]);
        exit;
    }

}
