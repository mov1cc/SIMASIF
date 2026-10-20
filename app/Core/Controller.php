<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\ValidationException;

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/main', int $status = 200): Response
    {
        return Response::html(View::render($view, $data, $layout), $status);
    }

    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    protected function redirect(string $path): Response
    {
        return Response::redirect(url($path));
    }

    protected function back(Request $request, string $fallback = '/'): Response
    {
        return Response::redirect($request->referer() ?? url($fallback));
    }

    
    protected function validate(Request $request, array $rules, array $messages = [], array $labels = []): array
    {
        $validator = Validator::make($request->all() + $request->files(), $rules, $messages, $labels);

        if ($validator->fails()) {
            throw new ValidationException(
                $validator->errors(),
                $request->except(['_token', '_method', 'password', 'password_confirmation'])
            );
        }

        return $validator->validated();
    }

    protected function user(): ?array
    {
        return Auth::user();
    }
}