<?php

declare(strict_types=1);

namespace App\Service\VacancyGrabber\Factory;

use App\Component\Common\Exception\Validation\InvalidArgumentException;
use App\Component\Source\Entity\Source;
use App\Contract\Dictionary\VacancyGrabberInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

use function array_find;
use function iterator_to_array;

/**
 * @property VacancyGrabberInterface[] $grabbers
 */
final readonly class GrabberFactory
{
    public function __construct(
        #[AutowireIterator('app.vacancy_grabber')]
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
