<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('payouts:process-monthly')->monthlyOn(1, '09:00')->withoutOverlapping();
Schedule::command('rates:sync')->hourly()->withoutOverlapping();
Schedule::command('carts:clean')->dailyAt('03:00')->withoutOverlapping();
