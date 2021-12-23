<?php

namespace App\Entity;

use App\Repository\EDTRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EDTRepository::class)
 */
class EDT
{
//    /**
//     * @ORM\Id
//     * @ORM\GeneratedValue
//     * @ORM\Column(type="integer")
//     */
//    private $id;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=10)
     */
    private $periode;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $jour;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $heureDebut;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $heureFin;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="eDTs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idKine;

//    public function getId(): ?int
//    {
//        return $this->id;
//    }

    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    public function setPeriode(string $periode): self
    {
        $this->periode = $periode;

        return $this;
    }

    public function getJour(): ?int
    {
        return $this->jour;
    }

    public function setJour(int $jour): self
    {
        $this->jour = $jour;

        return $this;
    }

    public function getHeureDebut(): ?string
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(string $heureDebut): self
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?string
    {
        return $this->heureFin;
    }

    public function setHeureFin(string $heureFin): self
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    public function getIdKine(): ?User
    {
        return $this->idKine;
    }

    public function setIdKine(?User $idKine): self
    {
        $this->idKine = $idKine;

        return $this;
    }
}
