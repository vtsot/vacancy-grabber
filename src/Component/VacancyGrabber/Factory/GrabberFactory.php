<?php

declare(strict_types=1);

namespace App\Component\VacancyGrabber\Factory;

use App\Component\General\Exception\Validation\InvalidArgumentException;
use App\Component\VacancyGrabber\Contract\VacancyGrabberInterface;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

use function array_find;
use function iterator_to_array;

/**
 * @property VacancyGrabberInterface[] $grabbers
 */
final readonly class GrabberFactory
{
    public function __construct(
        #[AutowireIterator('app.vacancy_grabber_handler')]
        private iterable $grabbers,
    ) {
    }

    public function create(Source $source): VacancyGrabberInterface
    {
        if ($grabber = $this->resolveGrabber($source)) {
            return $grabber;
        }

        throw new InvalidArgumentException('Grabber not found');
    }

    private function resolveGrabber(Source $source): ?VacancyGrabberInterface
    {
        return array_find(
            iterator_to_array($this->grabbers),
            static fn (VacancyGrabberInterface $grabber) => $grabber->getGrabber() === $source->getGrabber()
        );
    }
}
