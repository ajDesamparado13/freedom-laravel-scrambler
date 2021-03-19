<?php

namespace Freedom\Scrambler\Facades;

use Illuminate\Support\Facades\Facade;

class Scrambler extends Facade{

    protected static function getFacadeAccessor()
    {
        return 'scrambler';
    }

}
