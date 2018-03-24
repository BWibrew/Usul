<?php

namespace App\Http\Controllers;

use App\Site;
use Exception;
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
    public function showAuthSettings(Site $site)
    {
        return view('sites.auth', [
            'site' => $site,
        ]);
    }

    /**
     * Handle authentication request.
     *
     * @param Site $site
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Site $site, Request $request)
    {
        $request->validate([
            'type' => 'required',
            'username' => 'required',
            'password' => 'required',
        ]);

        switch (strtolower($request->type)) {
            case 'jwt':
                return $this->authenticateWithJwt($site, $request);
        }

        return redirect()->route('sites.editAuth', $site)->withInput()->with('status', 'Incorrect auth type.');
    }

    protected function authenticateWithJwt(Site $site, Request $request)
    {
        $status = null;
        $authResponse = null;

        try {
            $authResponse = app(Wordpress::class)->jwtAuth($site->root_uri, [
                'username' => $request->username,
                'password' => $request->password,
            ]);
        } catch (Exception $exception) {
            report($exception);
            $status = 'API connection problem. Error code: '.$exception->getCode();
        }

        $site->auth_type = 'jwt';
        $site->auth_token = $authResponse['token'];
        $site->save();

        return redirect()->route('sites.editAuth', $site)->withInput()->with('status', $status);
    }
}
