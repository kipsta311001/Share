<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThemeRepository::class)
 */
class Theme
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $libelle;

    /**
     * @ORM\ManyToMany(targetEntity=Fichier::class, mappedBy="themes")
     */
    private $themes;

    public function __construct()
    {
        $this->themes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|Fichier[]
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(Fichier $theme): self
    {
        if (!$this->themes->contains($theme)) {
            $this->themes[] = $theme;
            $theme->addTheme($this);
        }

        return $this;
    }

    public function removeTheme(Fichier $theme): self
    {
        if ($this->themes->removeElement($theme)) {
            $theme->removeTheme($this);
        }

        return $this;
    }
}
