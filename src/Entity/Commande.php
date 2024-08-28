<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomClient = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalPrix = null;

    /**
     * @var Collection<int, CommandeMateriel>
     */
    #[ORM\OneToMany(targetEntity: CommandeMateriel::class, mappedBy: 'commande')]
    private Collection $commandeMateriels;

    public function __construct()
    {
        $this->commandeMateriels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomClient(): ?string
    {
        return $this->nomClient;
    }

    public function setNomClient(string $nomClient): static
    {
        $this->nomClient = $nomClient;

        return $this;
    }

    public function getTotalPrix(): ?string
    {
        return $this->totalPrix;
    }

    public function setTotalPrix(string $totalPrix): static
    {
        $this->totalPrix = $totalPrix;

        return $this;
    }

    /**
     * @return Collection<int, CommandeMateriel>
     */
    public function getCommandeMateriels(): Collection
    {
        return $this->commandeMateriels;
    }

    public function addCommandeMateriel(CommandeMateriel $commandeMateriel): static
    {
        if (!$this->commandeMateriels->contains($commandeMateriel)) {
            $this->commandeMateriels->add($commandeMateriel);
            $commandeMateriel->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeMateriel(CommandeMateriel $commandeMateriel): static
    {
        if ($this->commandeMateriels->removeElement($commandeMateriel)) {
            // set the owning side to null (unless already changed)
            if ($commandeMateriel->getCommande() === $this) {
                $commandeMateriel->setCommande(null);
            }
        }

        return $this;
    }
}
