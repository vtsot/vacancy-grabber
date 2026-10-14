<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\SourceGrabber;

use App\VacancyGrabber\Domain\Shared\Exception\InvalidArgumentException;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\Source\Grabber\Grabber;
use App\VacancyGrabber\Domain\Source\Grabber\GrabberProvider;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

use function array_find;
use function iterator_to_array;

final readonly class TaggedGrabberProvider implements GrabberProvider
{
    /**
     * @param iterable<Grabber> $grabbers
     */
    public function __construct(
        #[AutowireIterator('app.vacancy_Source_grabber_handler')]
        private iterable $grabbers,
    ) {
    }

    public function forSource(Source $source): Grabber
    {
        $grabber = array_find(
            iterator_to_array($this->grabbers),
            static fn (Grabber $grabber) => $grabber->getGrabber() === $source->getGrabber()
        );

        return $grabber ?? throw new InvalidArgumentException('Grabber not found');
    }
}
