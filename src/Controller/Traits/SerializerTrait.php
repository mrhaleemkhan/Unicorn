<?php

namespace App\Controller\Traits;


use Symfony\Component\Serializer\SerializerInterface;

/**
 * Initiate Symfony Serializer for better reach in controller
 *
 * Trait SerializerTrait
 * @package App\Controller\Traits
 */
trait SerializerTrait
{

    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

}