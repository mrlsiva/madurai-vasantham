<?php

namespace App\Http\Middleware;

use Closure;
use Config;
class validateHostsMiddleware {	
	/*
	 * Get & assign allowed hosts list config values
	*/
	public function __construct() {		
		$this->hosts = Config::get('attendance.allowed_hosts');
	}
  
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)  {
      $host = $request->getHost();
      if (!in_array($host, $this->hosts)) {
        return Response()->json(['response' => 'The request hostname is invalid.'], 400);
      }
      return $next($request);
  }
}