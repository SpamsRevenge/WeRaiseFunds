<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
	public function handle(Request $request, Closure $next)
	{
		// if (Auth::check() && Auth::user()->hasAnyRole(['super-admin', 'admin'])) {
			return $next($request);
		// }

		// return redirect('/login');
	}
}  