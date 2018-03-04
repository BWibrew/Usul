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
     * @param Wordpress $wp
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site, Wordpress $wp)
    {
        $status = null;

        try {
            if (is_null($site->root_uri) || ! $wp->apiConnected($site->root_uri)) {
                $status = 'Could not connect to API!';
            }
        } catch (Exception $exception) {
            report($exception);
            $status = 'Could not connect to API!';
        }

        return view('sites.detail', [
            'site' => $site,
            'status' => $status,
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
        $wp = resolve('ApiConnections\Wordpress');

        try {
            $site->root_uri = $wp->discover($site->url);
        } catch (Exception $exception) {
            report($exception);
        }

        try {
            $site->name = is_null($site->root_uri) ? null : $wp->siteName($site->root_uri);
        } catch (Exception $exception) {
            report($exception);
        }

        $site->save();

        return $site;
    }
}
