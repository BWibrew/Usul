<?php

namespace App\Http\Controllers;

use App\Site;
use Illuminate\Http\Request;
use App\ApiConnections\Wordpress;

class SiteAuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the authentication settings menu.
     *
     * @param Site $site
     * @param Wordpress $wpConnection
     *
     * @return \Illuminate\Http\Response
     */
    public function showAuthSettings(Site $site, Wordpress $wpConnection)
    {
        return view('sites.auth', [
            'site' => $site,
        ]);
    }

    /**
     * Handle authentication request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        return $this->authenticateWithJwt($request);
    }

    public function authenticateWithJwt(Request $request)
    {
        //
    }
}
