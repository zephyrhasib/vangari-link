
<?php
session_start();


if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user_id'])) {
    $_SESSION['user_id'] = (int)$_COOKIE['remember_user_id'];
    $_SESSION['role'] = $_COOKIE['remember_role'] ?? '';
    $_SESSION['name'] = $_COOKIE['remember_name'] ?? '';
    $_SESSION['profile_pic'] = $_COOKIE['remember_profile_pic'] ?? '';
}

$url = $_GET['url'] ?? 'home/index';

$parts = explode('/', trim($url, '/'));

$module = $parts[0] ?? 'home';
$action = $parts[1] ?? 'index';

$controllerFile = __DIR__ . '/../app/' . $module . '/controllers/' . ucfirst($module) . 'Controller.php';
$controllerClass = ucfirst($module) . 'Controller';

if (!file_exists($controllerFile)) {
    die('Page not found');
}

require_once $controllerFile;

if (!class_exists($controllerClass)) {
    die('Controller not found');
}

$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    die('Page not found');
}

$controller->$action();
exit;
?>
