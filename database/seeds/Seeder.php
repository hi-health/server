<?php

use Illuminate\Database\Seeder as SeederContract;

class Seeder extends SeederContract
{
    public function info($message)
    {
        $this->command
            ->getOutput()
            ->writeln($message);
    }
}
