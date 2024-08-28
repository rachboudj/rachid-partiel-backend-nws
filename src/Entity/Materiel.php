<?php

namespace App\Entity;

use App\Repository\MaterielRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterielRepository::class)]
class Materiel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixLocation = null;

    /**
     * @var Collection<int, CommandeMateriel>
     */
    #[ORM\OneToMany(targetEntity: CommandeMateriel::class, mappedBy: 'materiel')]
    private Collection $commandeMateriels;

    public function __construct()
    {
        $this->commandeMateriels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixLocation(): ?string
    {
        return $this->prixLocation;
    }

    public function setPrixLocation(string $prixLocation): static
    {
        $this->prixLocation = $prixLocation;

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
            $commandeMateriel->setMateriel($this);
        }

        return $this;
    }

    public function removeCommandeMateriel(CommandeMateriel $commandeMateriel): static
    {
        if ($this->commandeMateriels->removeElement($commandeMateriel)) {
            // set the owning side to null (unless already changed)
            if ($commandeMateriel->getMateriel() === $this) {
                $commandeMateriel->setMateriel(null);
            }
        }

        return $this;
    }
}
