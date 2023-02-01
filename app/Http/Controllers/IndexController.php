<?php

namespace App\Http\Controllers;

use App\Crawler\Crawler;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crawl(Request $request, Crawler $crawler)
    {
        $crawler->crawl($request->input('url'));
        return view('welcome', ['result' => $crawler->getResults()]);
    }
}
