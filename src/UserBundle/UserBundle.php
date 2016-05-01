<?php
/**
 * Created by PhpStorm.
 * User: saulius.vaitkevicius
 * Date: 3/21/2016
 * Time: 2:05 PM
 */

namespace UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
