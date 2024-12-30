<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    /**
     * @var Collection<int, SubCategory>
     */
    #[ORM\ManyToMany(targetEntity: SubCategory::class, inversedBy: 'services')]
    private Collection $subCategories;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(nullable: true)]
    private ?int $stock = null;

    /**
     * @var Collection<int, ServiceHistory>
     */
    #[ORM\OneToMany(targetEntity: ServiceHistory::class, mappedBy: 'service', orphanRemoval: true)]
    private Collection $serviceHistories;

    /**
     * @var Collection<int, OrderServices>
     */
    #[ORM\OneToMany(targetEntity: OrderServices::class, mappedBy: 'service', orphanRemoval: true)]
    private Collection $orderServices;

    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
        $this->serviceHistories = new ArrayCollection();
        $this->orderServices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, SubCategory>
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(SubCategory $subCategory): static
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories->add($subCategory);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory): static
    {
        $this->subCategories->removeElement($subCategory);

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection<int, ServiceHistory>
     */
    public function getServiceHistories(): Collection
    {
        return $this->serviceHistories;
    }

    public function addServiceHistories(ServiceHistory $serviceHistories): static
    {
        if (!$this->serviceHistories->contains($serviceHistories)) {
            $this->serviceHistories->add($serviceHistories);
            $serviceHistories->setService($this);
        }

        return $this;
    }

    public function removeServiceHistories(ServiceHistory $serviceHistories): static
    {
        if ($this->serviceHistories->removeElement($serviceHistories)) {
            // set the owning side to null (unless already changed)
            if ($serviceHistories->getService() === $this) {
                $serviceHistories->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderServices>
     */
    public function getOrderServices(): Collection
    {
        return $this->orderServices;
    }

    public function addOrderService(OrderServices $orderService): static
    {
        if (!$this->orderServices->contains($orderService)) {
            $this->orderServices->add($orderService);
            $orderService->setService($this);
        }

        return $this;
    }

    public function removeOrderService(OrderServices $orderService): static
    {
        if ($this->orderServices->removeElement($orderService)) {
            // set the owning side to null (unless already changed)
            if ($orderService->getService() === $this) {
                $orderService->setService(null);
            }
        }

        return $this;
    }
}
