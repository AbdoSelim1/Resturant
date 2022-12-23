<?php

use Illuminate\Support\Str;

function redirectAccordingToRequest($request, string $responseStatus = "success")
{
    if ($responseStatus == 'success') {
        $message = 'Created Successfilly';
    } else {
        $message = 'Something went Wronge!';
    }
    if ($request->has('create')) {
        $routeName = Str::replace('store', 'index', $request->route()->getName());
        return redirect()->route($routeName)->with($responseStatus, $message);
    } elseif ($request->has('create_return')) {
        return redirect()->back()->with($responseStatus, $message);
    }
}
