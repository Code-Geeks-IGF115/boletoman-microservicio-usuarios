<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $userr = null;

    #[ORM\Column(length: 150)]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: MetodoPago::class)]
    private Collection $metodosPago;

    public function __construct()
    {
        $this->metodosPago = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserr(): ?string
    {
        return $this->userr;
    }

    public function setUserr(?string $userr): self
    {
        $this->userr = $userr;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, MetodoPago>
     */
    public function getMetodosPago(): Collection
    {
        return $this->metodosPago;
    }

    public function addMetodosPago(MetodoPago $metodosPago): self
    {
        if (!$this->metodosPago->contains($metodosPago)) {
            $this->metodosPago->add($metodosPago);
            $metodosPago->setUsuario($this);
        }

        return $this;
    }

    public function removeMetodosPago(MetodoPago $metodosPago): self
    {
        if ($this->metodosPago->removeElement($metodosPago)) {
            // set the owning side to null (unless already changed)
            if ($metodosPago->getUsuario() === $this) {
                $metodosPago->setUsuario(null);
            }
        }

        return $this;
    }
}
