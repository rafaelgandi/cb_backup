<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // Exclude api calls
		// See: https://mattstauffer.co/blog/excluding-routes-from-the-csrf-middleware-in-laravel-5.1
		// See: https://laravel-news.com/2015/06/excluding-routes-from-the-csrf-middleware/?utm_medium=referral&utm_source=mattstauffer.co&utm_campaign=matt-loves-laravel-news
		'api/*'
    ];
}
