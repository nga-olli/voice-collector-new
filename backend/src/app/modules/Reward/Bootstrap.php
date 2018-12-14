<?php
namespace Reward;

use Phalcon\{
    DI,
    Events\Manager as PhEventsManager
};
use Shirou\Bootstrap as ShBootstrap;

class Bootstrap extends ShBootstrap
{
    protected $_moduleName = 'Reward';

    public function __construct(DI $di, PhEventsManager $em)
    {
        parent::__construct($di, $em);
    }
}
