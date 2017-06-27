<?php

namespace App\Http\Controllers;

use App\Jobs\DownloadHtml;
use Illuminate\Http\Request;

class QueueExampleController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|url'
        ]);

        dispatch(new DownloadHtml($request->input('url')));

        return response()->json(['status' => 'ok'], 202);
    }
}
