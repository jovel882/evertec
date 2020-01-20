<?php

namespace App\Traits;

trait PaymentTrait
{
    private function getReference($id): String
    {
        return 'PTP_'.$id.time();
    }
    private function getUuid(): String
    {
        return (string) \Illuminate\Support\Str::uuid();
    }
}
