<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\EntityTrait;

// use Doctrine\DBAL\Types\Types;
// use Doctrine\ORM\Mapping as ORM;

trait UrlEntityTrait
{
    //    #[ORM\Column(name: 'url', type: Types::STRING, length: 255, nullable: true)]
    private ?string $url = null;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }
}
