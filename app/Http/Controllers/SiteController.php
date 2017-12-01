<?php

namespace App\Http\Controllers;

use App\Site;
use Exception;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    protected $wp;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->wp = resolve('ApiConnections\Wordpress');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home', ['sites' => Site::all()]);
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

        try {
            $site = $this->populateFromApi($site);
        } catch (Exception $e) {
            return redirect()->route('sites.edit', $site)->with('discovery', 'fail');
        }

        return redirect()->route('sites.show', $site);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site)
    {
        //
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
    public function populateFromApi(Site $site)
    {
        $site->root_uri = $this->wp->discover($site->url);
        $site->name = $this->wp->siteName($site->root_uri);
        $site->save();

        return $site;
    }
}
