<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ObjectifRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Objectif
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", options={"default" : 0})
     */
    private $budget;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="objectifs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Type("int")
     */
    private $userQuantity;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Menu", mappedBy="objectif", cascade={"persist", "remove"})
     */
    private $menu;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vegetarian;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->budget;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUserQuantity(): ?int
    {
        return $this->userQuantity;
    }

    public function setUserQuantity(?int $userQuantity): self
    {
        $this->userQuantity = $userQuantity;

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;

        // set (or unset) the owning side of the relation if necessary
        $newObjectif = $menu === null ? null : $this;
        if ($newObjectif !== $menu->getObjectif()) {
            $menu->setObjectif($newObjectif);
        }

        return $this;
    }

    public function getVegetarian(): ?bool
    {
        return $this->vegetarian;
    }

    public function setVegetarian(?bool $vegetarian): self
    {
        $this->vegetarian = $vegetarian;

        return $this;
    }

    /**
     *
     * @ORM\PrePersist()
     *
     */
    public function prePersist()
    {
        // Force persist boolean value in DB
        $this->vegetarian = (bool) $this->vegetarian;
    }

    /**
     *
     * @ORM\PreUpdate()
     *
     */
    public function preUpdate()
    {
        // Force persist boolean value in DB
        $this->vegetarian = (bool) $this->vegetarian; //Force using boolean value 
    }

    /**
     * @Assert\Callback
     */
    /*public function validate(ExecutionContextInterface $context, $payload)
    {

        dump('Actual value is : ' . $this->budget);

        if (!is_float($this->budget)) {
            dump('Value is NOT FLOAT');
            $context->buildViolation('This is not a float type.')
                    ->atPath('budget')
                    ->addViolation();
            exit;
        } else {
            dump('Value is FLOAT'); 
            exit;
        }
    }*/
}
