<?php

namespace App\Controller;

use App\Controller\Traits\SerializerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class BaseController
 * @package App\Controller
 */
class BaseController extends AbstractController
{

    // Make Serializer Trait available in controller
    use SerializerTrait;
}