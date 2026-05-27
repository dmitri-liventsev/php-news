<?php

namespace App\Mudimind\Interface\Http\Client;

use App\Mudimind\Interface\Http\Client\Request\BookTimeRequest;
use App\Mudimind\Interface\Http\Client\Request\GetFreeSpotsRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class BookController extends AbstractController
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function getFreeSpots(GetFreeSpotsRequest $request): JsonResponse
    {
        $spots = $this->handle($request->toQuery());

        return $this->json($spots);
    }

    public function bookTime(BookTimeRequest $request): JsonResponse
    {
        $bookId = $this->handle($request->toCommand());

        return $this->json(['ok' => true, 'id' => $bookId->value], Response::HTTP_CREATED);
    }
}