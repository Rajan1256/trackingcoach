<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TestsMediaController extends Controller
{
    public function __invoke(Request $request, User $customer, Test $test, Media $media)
    {
        if ($media->model->id != $test->id || $test->user_id != $customer->id) {
            abort(404);
        }

        return $media;
    }
}
