<?php

class DealerController
{
    public function dashboard()
    {
        require_once __DIR__ . '/../views/dashboard.php';
    }

    public function account()
    {

        require_once __DIR__ . '/../views/account.php';
    }

    public function manage_prices()
    {

        require_once __DIR__ . '/../views/manage_prices.php';
    }

    public function manage_requests()
    {
        require_once __DIR__ . '/../views/manage_requests.php';
    }

}
