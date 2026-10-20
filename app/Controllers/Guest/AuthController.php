<?php

declare(strict_types=1);

namespace App\Controllers\Guest;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class AuthController extends Controller
{
    public function showLogin(): Response
    {
        return $this->view('guest/login', ['title' => 'Login']);
    }

    public function login(Request $request): Response
    {
        $data = $this->validate($request, [
            'identifier' => 'required|string|max:150',
            'password'   => 'required|string',
        ], [], ['identifier' => 'Email/Username', 'password' => 'Password']);

        if (!Auth::attempt($data['identifier'], $data['password'])) {
            Session::flash('error', 'Email/username atau password salah.');
            Session::flashInput(['identifier' => $data['identifier']]);

            return $this->redirect('/login');
        }

        return $this->redirect(Auth::home());
    }

    public function logout(): Response
    {
        Auth::logout();
        Session::flash('success', 'Anda telah keluar.');

        return $this->redirect('/login');
    }
}