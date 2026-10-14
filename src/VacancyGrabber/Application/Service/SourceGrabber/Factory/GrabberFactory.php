<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Service\SourceGrabber\Factory;

use App\Contract\SourceGrabberInterface;
use App\VacancyGrabber\Application\Exception\Validation\InvalidArgumentException;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

use function array_find;
use function iterator_to_array;

/**
 * @property SourceGrabberInterface[] $grabbers
 */
final readonly class GrabberFactory
{
    public function __construct(
        #[AutowireIterator('app.vacancy_Source_grabber_handler')]
        private iterable $grabbers,
    ) {
    }

    public function create(Source $source): SourceGrabberInterface
    {
        if ($grabber = $this->resolveGrabber($source)) {
            return $grabber;
        }

        throw new InvalidArgumentException('Grabber not found');
    }

    private function resolveGrabber(Source $source): ?SourceGrabberInterface
    {
        return array_find(
            iterator_to_array($this->grabbers),
            static fn (SourceGrabberInterface $grabber) => $grabber->getGrabber() === $source->getGrabber()
        );
    }
}
