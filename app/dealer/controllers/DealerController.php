<?php

require_once __DIR__ . '/../../validation/PriceValidation.php';
require_once __DIR__ . '/../../validation/AccountValidation.php';
require_once __DIR__ . '/../models/DealerPriceModel.php';
require_once __DIR__ . '/../models/DealerAccountModel.php';


class DealerController
{
    private function requireBuyer()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'buyer') {
            header("Location: index.php?url=household/dashboard");
            exit;
        }
    }

    public function dashboard()
    {
        $this->requireBuyer();
        require_once __DIR__ . '/../views/dashboard.php';
    }

    public function account()
    {
        $this->requireBuyer();

        $model = new DealerAccountModel();
        $user = $model->findDealerById((int)$_SESSION['user_id']);
        
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
        $this->requireBuyer();

        list($errors, $clean) = validateDealerProfileUpdate($_POST);
        if (!empty($errors)) {
            header("Location: index.php?url=dealer/account");
            exit;
        }

        $model = new DealerAccountModel();
        $uid = (int)$_SESSION['user_id'];

        if ($model->emailOrPhoneTakenByOthers($uid, $clean['email'], $clean['phone'])) {
            header("Location: index.php?url=dealer/account");
            exit;
        }

        $model->updateDealerProfile(
            $uid,
            $clean['name'],
            $clean['shop_name'],
            $clean['area'],
            $clean['phone'],
            $clean['email']
        );

        $_SESSION['name'] = $clean['name'];
        $_SESSION['flash'] = "Profile updated successfully.";

        header("Location: index.php?url=dealer/account");
        exit;
    }

    public function change_password()
    {
        $this->requireBuyer();

        list($errors, $clean) = validateDealerPasswordChange($_POST);
        if (!empty($errors)) {
            header("Location: index.php?url=dealer/account");
            exit;
        }

        $model = new DealerAccountModel();
        $uid = (int)$_SESSION['user_id'];

        if (!$model->verifyDealerPassword($uid, $clean['current'])) {
            header("Location: index.php?url=dealer/account");
            exit;
        }

        if ($model->verifyDealerPassword($uid, $clean['new'])) {
            header("Location: index.php?url=dealer/account");
            exit;
        }

        $hash = password_hash($clean['new'], PASSWORD_DEFAULT);
        $model->updateDealerPassword($uid, $hash);

        $_SESSION['flash'] = "Password changed successfully.";
        header("Location: index.php?url=dealer/account");
        exit;
    }

    public function delete_account()
    {
        $this->requireBuyer();

        $confirmText = trim($_POST['confirm_delete'] ?? '');
        $password    = $_POST['delete_password'] ?? '';

        if ($confirmText !== 'YES') {
            header("Location: index.php?url=dealer/account");
            exit;
        }

        $model = new DealerAccountModel();
        $uid = (int)$_SESSION['user_id'];

        if (!$model->verifyDealerPassword($uid, $password)) {
            header("Location: index.php?url=dealer/account");
            exit;
        }

        $model->deleteDealerAccount($uid);
        header("Location: index.php?url=auth/logout");
        exit;
    }

    public function manage_prices()
    {
        $this->requireBuyer();
        $model = new DealerPriceModel();
        $dealerId = (int)$_SESSION['user_id'];

        $rows = $model->getManagePriceRows($dealerId);
        $alreadySubmittedToday = $model->hasSubmittedToday($dealerId);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $errors = $_SESSION['errors'] ?? null;
        unset($_SESSION['errors']);

        $oldPrices = $_SESSION['old_prices'] ?? [];
        unset($_SESSION['old_prices']);
        require_once __DIR__ . '/../views/manage_prices.php';
    }


    public function save_prices()
    {
        $this->requireBuyer();
        $model = new DealerPriceModel();
        $dealerId = (int)$_SESSION['user_id'];

        $postedPrices = $_POST['prices'] ?? [];

        if ($model->hasSubmittedToday($dealerId)) {
            $_SESSION['errors'] = [
                "You already submitted prices today. Only one submission per day is allowed."
            ];
            $_SESSION['old_prices'] = $postedPrices;

            header("Location: index.php?url=dealer/manage_prices");
            exit;
        }

        $referenceRows = $model->getManagePriceRows($dealerId);

        [$cleanPrices, $errors] =
            PriceValidation::validateDealerPrices($postedPrices, $referenceRows, 5.0);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_prices'] = $postedPrices;

            header("Location: index.php?url=dealer/manage_prices");
            exit;
        }

        if (empty($cleanPrices)) {
            $_SESSION['errors'] = ["Please enter at least one price before saving."];
            $_SESSION['old_prices'] = $postedPrices;

            header("Location: index.php?url=dealer/manage_prices");
            exit;
        }

        $ok = $model->insertDealerPricesOnce($dealerId, $cleanPrices);

        $_SESSION['flash'] = $ok
            ? "Prices submitted successfully for today."
            : "Failed to submit prices.";

        unset($_SESSION['old_prices']);

        header("Location: index.php?url=dealer/manage_prices");
        exit;
    }

    public function manage_requests()
    {
        $this->requireBuyer();
        require_once __DIR__ . '/../views/manage_requests.php';
    }
}
