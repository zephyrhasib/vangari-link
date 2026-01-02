<?php
class HomeController
{
    public function about()
    {
        require_once __DIR__ . '/../views/about.php';
    }
    public function howitworks()
    {
        require_once __DIR__ . '/../views/howitworks.php';
    }

    public function contact()
    {
        require_once __DIR__ . '/../views/contact.php';
    }


    public function index()
    {
        require_once __DIR__ . '/../views/index.php';
    }


    
}
