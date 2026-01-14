<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/scaner', function(){
    return view("scaner.index");
});


Route::get('/scanner2', function(){
    return view("scaner.scaner");
});