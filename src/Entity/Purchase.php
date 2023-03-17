<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=PurchaseRepository::class)
 */
class Purchase
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $buyerName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $buyerEmail;


    /**
     * @Ignore()
     * @ORM\OneToOne(targetEntity=Unicorn::class, cascade={"persist", "remove"}, inversedBy="purchased")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unicorn;

    /**
     * @ORM\Column(type="datetime")
     */
    private $purchaseDate;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getBuyerName()
    {
        return $this->buyerName;
    }

    /**
     * @param mixed $buyerName
     */
    public function setBuyerName($buyerName): void
    {
        $this->buyerName = $buyerName;
    }

    /**
     * @return mixed
     */
    public function getUnicorn()
    {
        return $this->unicorn;
    }

    /**
     * @param mixed $unicorn
     */
    public function setUnicorn($unicorn): void
    {
        $this->unicorn = $unicorn;
    }

    /**
     * @return mixed
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * @param mixed $purchaseDate
     */
    public function setPurchaseDate($purchaseDate): void
    {
        $this->purchaseDate = $purchaseDate;
    }

    /**
     * @return mixed
     */
    public function getBuyerEmail()
    {
        return $this->buyerEmail;
    }

    /**
     * @param mixed $buyerEmail
     */
    public function setBuyerEmail($buyerEmail): void
    {
        $this->buyerEmail = $buyerEmail;
    }


}
