<?php

require_once __DIR__ . '/../models/HouseholdAccountModel.php';
require_once __DIR__ . '/../../helpers/AccountValidation.php';

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
        require_once __DIR__ . '/../views/check_prices.php';
    }


    public function create_request()
    {
        $this->requireSeller();
        require_once __DIR__ . '/../views/create_request.php';
    }

    public function order_tracking()
    {
        $this->requireSeller();
        require_once __DIR__ . '/../views/order_tracking.php';
    }

}
