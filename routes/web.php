<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Embed\Embed;
Route::get('/', function () {
    $embed = new Embed();

    //Load any url:
    $info = $embed->get('https://www.startinfinity.com?mijat=faca');
    // $providers = $info->getProviders();
    $meta = $info->getMetas();

    $document = $info->getDocument();
    $h1 = $document->select('.//h1')->str();
    $h2 = $document->select('.//h2');

    dd([
        'title' => $info->title, //The page title
        'description' => $info->description, //The page description
        'url' => (string)$info->url, //The canonical url
        'canonical' => (string)$info->providerUrl, //The provider url
        'image' => (string)$info->image, //The image choosen as main image
        'og:title'=> $meta->str('og:title'),
        'og:image'=> $meta->str('og:image'),
        'og:url'=> $meta->str('og:url'),
        'og:site_name'=> $meta->str('og:site_name'),
        'og:description'=> $meta->str('og:description'),
        'og:type'=> $meta->str('og:type'),
        'h1'=> $document->select('.//h1')->str(),
        'h2'=> $h2->strAll(),
    ]);


    return view('welcome');
});
