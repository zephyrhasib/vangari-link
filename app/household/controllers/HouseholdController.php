<?php
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
        require_once __DIR__ . '/../views/account.php';
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
