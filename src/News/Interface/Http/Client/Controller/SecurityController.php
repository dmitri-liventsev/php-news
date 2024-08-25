<?php

namespace App\News\Interface\Http\Client\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController
{
    private UserPasswordHasherInterface $passwordHasher;
    private UserProviderInterface $userProvider;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        UserProviderInterface $userProvider
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->userProvider = $userProvider;
    }

    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if ($username && $password) {
            try {
                $user = $this->userProvider->loadUserByIdentifier($username);
                if ($user && $this->passwordHasher->isPasswordValid($user, $password)) {
                    // Generate and return a token or session ID
                    return new JsonResponse(['status' => 'success', 'message' => 'Logged in successfully']);
                }
            } catch (AuthenticationException $e) {
                return new JsonResponse(['status' => 'error', 'message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
            }
        }

        return new JsonResponse(['status' => 'error', 'message' => 'Invalid request'], Response::HTTP_BAD_REQUEST);
    }

    public function logout(): JsonResponse
    {
        // Invalidate token or clear session
        return new JsonResponse(['status' => 'success', 'message' => 'Logged out successfully']);
    }
}