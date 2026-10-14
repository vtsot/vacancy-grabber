<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Service;

use App\Contract\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Stamp\StampInterface;

use function array_map;
use function count;
use function get_class;
use function implode;
use function sprintf;

final readonly class MessageBusHandler
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $queryBus,
        private MessageBusInterface $eventBus,
    ) {
    }

    /**
     * @param object|Envelope $message The message or the message pre-wrapped in an envelope
     * @param int             $delay   The delay in milliseconds
     */
    public function handleCommand(object $message, bool $dispatchAfterCurrentBus = false, int $delay = 0): mixed
    {
        $envelope = Envelope::wrap($message, $this->resolveStamps($dispatchAfterCurrentBus, $delay));

        return $this->getResult(
            envelope: $this->commandBus->dispatch($envelope),
            allowNoResult: $message instanceof AsyncMessageInterface
                || count($envelope->all(DispatchAfterCurrentBusStamp::class)),
        );
    }

    /**
     * @param object|Envelope $message The message or the message pre-wrapped in an envelope
     */
    public function handleQuery(object $message): mixed
    {
        return $this->getResult(
            envelope: $this->queryBus->dispatch($message),
        );
    }

    /**
     * @param object|Envelope $message The message or the message pre-wrapped in an envelope
     * @param int             $delay   The delay in milliseconds
     */
    public function handleEvent(object $message, bool $dispatchAfterCurrentBus = false, int $delay = 0): mixed
    {
        $envelope = Envelope::wrap($message, $this->resolveStamps($dispatchAfterCurrentBus, $delay));

        return $this->getResult(
            envelope: $this->eventBus->dispatch($envelope),
            allowNoResult: true,
        );
    }

    private function getResult(Envelope $envelope, bool $allowNoResult = false): mixed
    {
        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        if (!$handledStamps) {
            if ($allowNoResult) {
                return null;
            }

            throw new LogicException(
                sprintf(
                    'Message of type "%s" was handled zero times. Exactly one handler is expected when using "%s::%s()".',
                    get_class($envelope->getMessage()),
                    get_class($this),
                    __FUNCTION__,
                ),
            );
        }

        if (count($handledStamps) > 1) {
            $handlers = implode(
                ', ',
                array_map(
                    static function (HandledStamp $stamp): string {
                        return sprintf('"%s"', $stamp->getHandlerName());
                    },
                    $handledStamps,
                ),
            );

            throw new LogicException(
                sprintf(
                    'Message of type "%s" was handled multiple times. Only one handler is expected when using "%s::%s()", got %d: %s.',
                    get_class($envelope->getMessage()),
                    get_class($this),
                    __FUNCTION__,
                    count($handledStamps),
                    $handlers,
                ),
            );
        }

        return $handledStamps[0]->getResult();
    }

    /** @return StampInterface[] */
    private function resolveStamps(bool $dispatchAfterCurrentBus, int $delay): array
    {
        $stamps = [];

        if ($dispatchAfterCurrentBus) {
            $stamps[] = new DispatchAfterCurrentBusStamp();
        }

        if ($delay > 0) {
            $stamps[] = new DelayStamp($delay);
        }

        return $stamps;
    }
}
