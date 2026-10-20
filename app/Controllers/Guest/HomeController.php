<?php

declare(strict_types=1);

namespace App\Controllers\Guest;

use App\Core\Controller;
use App\Core\Response;

final class HomeController extends Controller
{
    public function index(): Response
    {
        return $this->view('guest/home', ['title' => 'Beranda']);
    }
}