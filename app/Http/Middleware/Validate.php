<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Validate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $request->validate([
                'url' => ['required', 'url', 'max:255'],
                'page_count' => ['required', 'integer', 'min:4', 'max:6'],
                ]);
        return $next($request);
    }
}
