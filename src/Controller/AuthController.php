<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/api')]
class AuthController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): JsonResponse
    {
        return new JsonResponse(['message' => 'Provide your email and password in JSON format'], 401);
    }
}
