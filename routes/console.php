<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('jobs:close-expired')->dailyAt('00:10');
