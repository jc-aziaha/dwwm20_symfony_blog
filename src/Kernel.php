<?php

namespace App;

use App\Trait\TimeZoneTrait;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    use TimeZoneTrait;

    public function __construct(string $environnement, bool $debug)
    {

        $this->changeTimeZone("Europe/Paris");

        parent::__construct($environnement, $debug);
    }
}
