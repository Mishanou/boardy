<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,      // localhost без HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();