<?php

namespace App\Entity;

use App\Repository\LinksRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinksRepository::class)]
class Links
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $impressum = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $datenschutz = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImpressum(): ?string
    {
        return $this->impressum;
    }

    public function setImpressum(?string $impressum): self
    {
        $this->impressum = $impressum;

        return $this;
    }

    public function getDatenschutz(): ?string
    {
        return $this->datenschutz;
    }

    public function setDatenschutz(?string $datenschutz): self
    {
        $this->datenschutz = $datenschutz;

        return $this;
    }
}
