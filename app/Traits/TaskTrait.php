<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait TaskTrait
{
    /**
     *  Date formatting
     *
     * @param \Illuminate\Support\Carbon $date
     *
     * @return string
     */
    protected function formateDate(Carbon $date) : string
    {
        return $date->format('d.m.Y');
    }
}
