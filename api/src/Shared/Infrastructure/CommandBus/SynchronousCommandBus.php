<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\CommandBus;

use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Command\CommandInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class SynchronousCommandBus implements CommandBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @template T
     * @param CommandInterface<T> $command
     * @throws ExceptionInterface
     */
    public function dispatch(CommandInterface $command): void
    {

        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $handlerFailedException) {
            if ($exception = current($handlerFailedException->getWrappedExceptions())) {
                throw $exception;
            }

            throw $handlerFailedException;
        }
    }
}
