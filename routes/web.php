<?php

use Livewire\Volt\Volt;

Volt::route('/', 'index')
    ->name('home');

Volt::route('/users', 'users.index')
    ->name('users.index');

Volt::route('/users/{user}/edit', 'users.edit')
    ->name('users.edit');
