<?php

namespace App\Http\Controllers;

use App\Site;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\ApiConnections\Wordpress;
use Illuminate\Database\Query\Builder;
use GuzzleHttp\Exception\GuzzleException;

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
            'url' => [
                'required',
                'url',
                Rule::unique('sites', 'url')->where(function (Builder $query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
        ]);

        $site = Site::create(['url' => trim($request->input('url'), '/\\')]);

        $site = $this->populateFromApi($site);

        if (is_null($site->root_uri)) {
            return redirect()->route('sites.edit', $site)->with('discovery', 'fail');
        }

        return redirect()->route('sites.show', $site);
    }

    /**
     * Display the specified resource.
     *
     * @param Site $site
     * @param Wordpress $wpConnection
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site, Wordpress $wpConnection)
    {
        $connection = [
            'wp_rest' => true,
            'site_monitor' => true,
            'authenticated' => true,
        ];

        $wpConnection->authType($site->auth_type ?: null);
        $wpConnection->authToken($site->auth_token ?: null);

        $connectionStatus = $this->getGetConnectionStatus($site, $wpConnection);

        if (is_null($site->root_uri) || ! $connectionStatus['connected']) {
            $connection['wp_rest'] = false;
            $connection['site_monitor'] = false;
            $connection['authenticated'] = false;
        }

        $connection['authenticated'] = $connectionStatus['authenticated'];

        $namespaces = $this->getNamespaces($site, $wpConnection);

        if (! array_search('wp-site-monitor/v1', $namespaces)) {
            $status['namespaces'] = 'WP Site Monitor not detected.';
            $connection['site_monitor'] = false;
        }

        if ($connection['site_monitor']) {
            $wpVersion = $this->getWpVersion($site, $wpConnection);
            $plugins = $this->getPlugins($site, $wpConnection);
        }

        return view('sites.detail', [
            'site' => $site,
            'wpVersion' => $wpVersion ?? 'Unknown',
            'status' => $status ?? [],
            'connection' => $connection,
            'isConnected' => $connection['wp_rest'],
            'plugins' => $plugins ?? [],
            'namespaces' => $namespaces,
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
        $site->url = trim($request->input('url'), '/\\');
        $site->root_uri = trim($request->input('root_uri'), '/\\');
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
     * @throws GuzzleException
     */
    protected function populateFromApi(Site $site)
    {
        $wpConnection = app(Wordpress::class);

        try {
            $uri = trim($wpConnection->discover($site->url), '/\\');
            $site->root_uri = empty($uri) ? null : $uri;
        } catch (GuzzleException $exception) {
            report($exception);
        }

        $site->name = is_null($site->root_uri) ? null : $wpConnection->siteName($site->root_uri);

        $site->save();

        return $site;
    }

    /**
     * Get the connection status of the site.
     *
     * @param Site $site
     * @param Wordpress $wpConnection
     * @param array $connectionStatus
     *
     * @return array
     */
    protected function getGetConnectionStatus(
        Site $site,
        Wordpress $wpConnection,
        array $connectionStatus = ['connected' => false, 'authenticated' => false]
    ): array {
        try {
            $connectionStatus = $wpConnection->connectionStatus($site->root_uri);
        } catch (GuzzleException $exception) {
            $connectionStatus['connected'] = $exception->getCode() === 401;
            report($exception);
        }

        return $connectionStatus;
    }

    /**
     * Get the namespaces of the site.
     *
     * @param Site $site
     * @param Wordpress $wpConnection
     * @param array $namespaces
     *
     * @return array
     */
    protected function getNamespaces(Site $site, Wordpress $wpConnection, array $namespaces = []): array
    {
        try {
            $namespaces = $wpConnection->namespaces($site->root_uri);
        } catch (GuzzleException $exception) {
            report($exception);
        }

        return $namespaces;
    }

    /**
     * Get the WordPress version of the site.
     *
     * @param Site $site
     * @param Wordpress $wpConnection
     * @param string $wpVersion
     *
     * @return string
     */
    protected function getWpVersion(Site $site, Wordpress $wpConnection, string $wpVersion = 'Unknown'): string
    {
        try {
            $wpVersion = $wpConnection->version($site->root_uri);
        } catch (GuzzleException $exception) {
            report($exception);
        }

        return $wpVersion;
    }

    /**
     * Get the plugins installed on the site.
     *
     * @param Site $site
     * @param Wordpress $wpConnection
     * @param array $plugins
     *
     * @return array
     */
    protected function getPlugins(Site $site, Wordpress $wpConnection, array $plugins = []): array
    {
        try {
            $plugins = $wpConnection->plugins($site->root_uri);
        } catch (GuzzleException $exception) {
            report($exception);
        }

        return $plugins;
    }
}
