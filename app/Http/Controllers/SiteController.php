<?php

namespace App\Http\Controllers;

use App\Site;
use Exception;
use Illuminate\Http\Request;
use App\ApiConnections\Wordpress;

class SiteController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sites.index', ['sites' => Site::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sites.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $site = Site::create(['url' => $request->input('url')]);

        $site = $this->populateFromApi($site);

        if (is_null($site->root_uri)) {
            return redirect()->route('sites.edit', $site)->with('discovery', 'fail');
        }

        return redirect()->route('sites.show', $site);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Site $site
     * @param Wordpress $wpConnection
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site, Wordpress $wpConnection)
    {
        $status = null;
        $connection = [
            'wp_rest' => true,
            'site_monitor' => true,
            'authenticated' => true,
        ];

        $wpConnection->authType($site->auth_type ?: null);
        $wpConnection->authToken($site->auth_token ?: null);

        try {
            if (is_null($site->root_uri) || ! $wpConnection->apiConnected($site->root_uri)) {
                $connection['wp_rest'] = false;
            }
        } catch (Exception $exception) {
            report($exception);
            $status = 'API connection problem. Error code: '.$exception->getCode();
            $connection['wp_rest'] = false;
        }

        try {
            $wpVersion = $wpConnection->version($site->root_uri);
        } catch (Exception $exception) {
            report($exception);
            $status = 'API connection problem. Error code: '.$exception->getCode();
            $connection['authenticated'] = false;
        }

        try {
            $plugins = $wpConnection->plugins($site->root_uri);
        } catch (Exception $exception) {
            report($exception);
            $status = 'API connection problem. Error code: '.$exception->getCode();
            $connection['authenticated'] = false;
        }

        return view('sites.detail', [
            'site' => $site,
            'wpVersion' => $wpVersion ?? 'Unknown',
            'status' => $status,
            'connection' => $connection,
            'isConnected' => $connection['wp_rest'],
            'plugins' => $plugins ?? [],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        return view('sites.edit', ['site' => $site]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site)
    {
        $request->validate([
            'name' => 'required',
            'url' => 'required|url',
            'root_uri' => 'required|url',
        ]);

        $site->name = $request->get('name');
        $site->url = $request->get('url');
        $site->root_uri = $request->get('root_uri');
        $site->save();

        return redirect()->route('sites.edit', $site);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Site $site
     *
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('sites.index');
    }

    /**
     * Store a new Site model and populate fields using the API response.
     *
     * @param Site $site
     *
     * @return Site
     */
    protected function populateFromApi(Site $site)
    {
        $wpConnection = app(Wordpress::class);

        try {
            $site->root_uri = $wpConnection->discover($site->url);
        } catch (Exception $exception) {
            report($exception);
        }

        try {
            $site->name = is_null($site->root_uri) ? null : $wpConnection->siteName($site->root_uri);
        } catch (Exception $exception) {
            report($exception);
        }

        $site->save();

        return $site;
    }
}
