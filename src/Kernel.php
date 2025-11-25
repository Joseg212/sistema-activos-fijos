<?php

namespace App;
/**
* Developer: José Hernández
* email: jghernandez053@gmail.com
**/

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
