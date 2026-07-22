<?php

namespace Core;

use Core\Session;
use Core\Response;

class Controller
{
    protected Session $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function loadView($view, $data = null)
    {
        if(is_array($data)) {
            extract($data);
        }

        require_once view($view);
    }

    public function loadPage($view, array $data = [])
    {
        $data['flash'] = [
            'success' => $this->session->getFlash('success'),
            'message' => $this->session->getFlash('message'),
            'error' => $this->session->getFlash('error'),
        ];

        $this->loadView('includes/header', $data);

        $this->loadView($view, $data);

        $this->loadView('includes/footer');
    }

    public function redirect(string $location): never
    {
        header("Location: {$location}");

        exit;
    }

    public function fail(string $message, string $redirect): never
    {
        $this->session->flash('error', $message);

        $this->redirect($redirect);
    }

    public function success(string $message, string $redirect): never
    {
        $this->session->flash('message', $message);

        $this->redirect($redirect);
    }

    public function back(): never
    {
        header("Location: {$_SERVER['HTTP_REFERER']}");

        exit;
    }

    public function abort(int $code): never
    {
        Response::abort($code);
    }
}