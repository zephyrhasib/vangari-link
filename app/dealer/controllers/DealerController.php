<?php

require_once __DIR__ . '/../../validation/PriceValidation.php';
require_once __DIR__ . '/../../validation/AccountValidation.php';
require_once __DIR__ . '/../models/DealerPriceModel.php';
require_once __DIR__ . '/../models/DealerAccountModel.php';
require_once __DIR__ . '/../../validation/ProfileImageValidation.php';
require_once __DIR__ . '/../../models/ProfileImageModel.php';
require_once __DIR__ . '/../models/ManageRequestModel.php';


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

    private function requireBuyerPostAccess()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'buyer') {
            echo json_encode(['success' => false, 'errors' => ['Access denied.']]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'errors' => ['Invalid request method.']]);
            exit;
        }
    }

    private function requireJsonHeader()
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    private function requireBuyerJson()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'buyer') {
            echo json_encode(['success' => false, 'errors' => ['Access denied.']]);
            exit;
        }
    }

    //functions for dashboard
    public function dashboard()
    {
        $this->requireBuyer();
        require_once __DIR__ . '/../views/dashboard.php';
    }

    //functions for account management
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


    //functions for managing prices
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
    

    //functions for managing pickup requests
    public function manage_requests()
    {
        $this->requireBuyer();
        require_once __DIR__ . '/../views/manage_requests.php';
    }

    public function manage_requests_data()
    {
        $this->requireJsonHeader();

        $this->requireBuyerJson();

        $buyerId = (int)$_SESSION['user_id'];
        $model = new ManageRequestModel();

        $buyerInfo = $model->getBuyerInfo($buyerId);
        if (!$buyerInfo) {
            echo json_encode(['success' => false, 'errors' => ['Buyer profile not found.']]);
            exit;
        }

        $pending = $model->getPendingRequestsByArea($buyerInfo['area']);
        $myOrders = $model->getMyAcceptedRequests($buyerId);

        echo json_encode([
            'success' => true,
            'pending' => $pending,
            'myOrders' => $myOrders,
            'area' => $buyerInfo['area']
        ]);
        exit;
    }

    public function accept_request()
    {
        $this->requireJsonHeader();

        $this->requireBuyerPostAccess();

        $requestId = (int)($_POST['request_id'] ?? 0);
        if ($requestId <= 0) {
            echo json_encode(['success' => false, 'errors' => ['Invalid request.']]);
            exit;
        }

        $buyerId = (int)$_SESSION['user_id'];
        $model = new ManageRequestModel();

        $ok = $model->acceptRequest($requestId, $buyerId);

        if (!$ok) {
            echo json_encode(['success' => false, 'errors' => ['This request is no longer available.']]);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Request accepted.']);
        exit;
    }

    public function mark_dispatched()
    {
        $this->requireJsonHeader();

        $this->requireBuyerPostAccess();

        $requestId = (int)($_POST['request_id'] ?? 0);
        $buyerId = (int)$_SESSION['user_id'];

        $model = new ManageRequestModel();
        $ok = $model->markDispatched($requestId, $buyerId);

        if (!$ok) {
            echo json_encode(['success' => false, 'errors' => ['Cannot update status.']]);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Marked as dispatched.']);
        exit;
    }


    public function mark_collected()
    {
        $this->requireJsonHeader();

        $this->requireBuyerPostAccess();

        $requestId = (int)($_POST['request_id'] ?? 0);
        $buyerId = (int)$_SESSION['user_id'];

        $model = new ManageRequestModel();
        $ok = $model->markCollected($requestId, $buyerId);

        if (!$ok) {
            echo json_encode(['success' => false, 'errors' => ['Cannot update status.']]);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Marked as collected.']);
        exit;
    }

    //functions for order history
    public function order_history()
    {
        $this->requireBuyer();
        require_once __DIR__ . '/../views/order_history.php';
    }

    public function order_history_data()
    {
        $this->requireJsonHeader();

        $this->requireBuyerJson();

        $buyerId = (int)$_SESSION['user_id'];

        $limit = (int)($_GET['limit'] ?? 2);
        $offset = (int)($_GET['offset'] ?? 0);

        
        if ($limit <= 0) $limit = 2;
        if ($limit > 20) $limit = 20;
        if ($offset < 0) $offset = 0;

        require_once __DIR__ . '/../models/ManageRequestModel.php';
        $model = new ManageRequestModel();

        $total = $model->countHistory($buyerId);
        $rows = $model->getHistory($buyerId, $limit, $offset);

        $newOffset = $offset + count($rows);
        $hasMore = ($newOffset < $total);

        echo json_encode([
            'success' => true,
            'rows' => $rows,
            'total' => $total,
            'newOffset' => $newOffset,
            'hasMore' => $hasMore
        ]);
        exit;
    }


    // functions for profile photo upload
    public function upload_photo()
    {
        $this->requireBuyer();

        $flash = $_SESSION['flash'] ?? '';
        unset($_SESSION['flash']);

        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        require_once __DIR__ . '/../views/upload_photo.php';
    }

    public function save_photo()
    {
        $this->requireBuyer();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=dealer/upload_photo");
            exit;
        }

        list($errors, $clean) = validateProfileImageUpload($_FILES['profile_pic'] ?? []);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: index.php?url=dealer/upload_photo");
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $ext = $clean['ext'];

        $newName = "profile_" . $userId . "_" . time() . "." . $ext;

        $uploadDir = __DIR__ . '/../../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $targetPath = $uploadDir . $newName;
        
    
        if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
            $_SESSION['errors'] = ["Upload failed. Try again."];
            header("Location: index.php?url=dealer/upload_photo");
            exit;
        }

        $dbPath = "uploads/" . $newName;

        $model = new ProfileImageModel();
        $ok = $model->upsert($userId, $dbPath);

        if (!$ok) {
            $_SESSION['errors'] = ["Database update failed."];
            header("Location: index.php?url=dealer/upload_photo");
            exit;
        }
    
        $_SESSION['profile_pic'] = $dbPath;
        $_SESSION['flash'] = "Profile picture updated successfully.";

        header("Location: index.php?url=dealer/dashboard");
        exit;
    }
}
