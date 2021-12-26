<?php

namespace App\Entity;

use App\Repository\RDVRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RDVRepository::class)
 */
class RDV
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=5)
     */
    private $heureDebut;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="rDVs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $kine;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="rDVs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\Column(type="date")
     */
    private $date;

    public function getHeureDebut(): ?string
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(string $heureDebut): self
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getKine(): ?User
    {
        return $this->kine;
    }

    public function setKine(?User $kine): self
    {
        $this->kine = $kine;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
