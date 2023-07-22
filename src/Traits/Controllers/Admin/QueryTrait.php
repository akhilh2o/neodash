<?php

namespace Akhilesh\Neodash\Traits\Controllers\Admin;

use Illuminate\Http\Request;
use Akhilesh\Neodash\Models\Query;

trait QueryTrait
{
    public function index(Request $request)
    {
        $queries = Query::latest()->paginate(100);
        return view('admin.queries.index')->with([
            'queries' => $queries
        ]);
    }

    public function show(Query $query)
    {
        return view('admin.queries.show')->with([
            'query' =>  $query
        ]);
    }

    public function destroy(Query $query)
    {
        $query->delete();
        return redirect()->route('admin.queries.index')->withSuccess('SUCCESS !! Query has been deleted.');
    }
}
