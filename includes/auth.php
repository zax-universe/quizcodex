<?php
require_once __DIR__ . '/db.php';

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function currentUser() {
    if (!isLoggedIn()) return null;
    return DB::findUserById($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /pages/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function login($username, $password) {
    $user = DB::findUser($username);
    if (!$user) return ['error' => 'Username tidak ditemukan'];
    if (!password_verify($password, $user['password'])) return ['error' => 'Password salah'];
    startSession();
    $_SESSION['user_id'] = $user['id'];
    return $user;
}

function logout() {
    startSession();
    session_destroy();
    header('Location: /index.php');
    exit;
}
