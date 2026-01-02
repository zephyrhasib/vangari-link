<?php
class HouseholdController
{
    public function dashboard()
    {
        
        require_once __DIR__ . '/../views/dashboard.php';
    }
    public function account()
    {
        require_once __DIR__ . '/../views/account.php';
    }
    
    public function check_prices()
    {
        require_once __DIR__ . '/../views/check_prices.php';
    }


    public function create_request()
    {
        require_once __DIR__ . '/../views/create_request.php';
    }

    public function order_tracking()
    {
        require_once __DIR__ . '/../views/order_tracking.php';
    }

}
