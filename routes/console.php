<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:clean')->weekly();
Schedule::command('backup:run --only-db')->weekly();
