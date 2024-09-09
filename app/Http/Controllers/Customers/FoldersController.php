<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Folders\StoreFolderRequest;
use App\Models\AssetFolder;
use App\Models\User;
use Illuminate\Http\Request;

use function current_team;
use function redirect;

class FoldersController extends Controller
{
    public function store(StoreFolderRequest $request, User $customer)
    {
        AssetFolder::create([
            'name'      => $request->get('name'),
            'user_id'   => $customer->id,
            'team_id'   => current_team()->id,
            'parent_id' => $request->get('folder'),
        ]);

        session()->flash('message', __('Folder successfully created'));

        return redirect()->back();
    }

    public function destroy(Request $request, User $customer, AssetFolder $folder)
    {
        $folder->deleteWithChildren();

        session()->flash('message', __('Folder successfully deleted'));

        return redirect()->to(route('customers.files.index', [$customer]));
    }
}
