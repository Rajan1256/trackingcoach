<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Files\StoreFileRequest;
use App\Models\Asset;
use App\Models\AssetFolder;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function redirect;

class FilesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Asset::class, 'file');
    }

    public function index(Request $request, User $customer)
    {
        $currentFolder = $request->get('folder') ? AssetFolder::forCustomer($customer)->findOrFail($request->get('folder')) : null;
        $folders = AssetFolder::forCustomer($customer)->forFolder($currentFolder)->orderBy('name')->get();
        $files = Asset::forCustomer($customer)->forFolder($currentFolder)->get();

        return view('customers.files.index', [
            'customer'      => $customer,
            'folders'       => $folders,
            'currentFolder' => $currentFolder,
            'files'         => $files,
        ]);
    }

    public function show(Request $request, User $customer, Asset $file)
    {
        $actualFile = $file->file();

        return Storage::disk($actualFile->getDiskDriverName())
            ->download($actualFile->getPath(), $actualFile->file_name);
    }

    public function store(StoreFileRequest $request, User $customer)
    {
        if (!$request->hasFile('files')) {
            return redirect()->back();
        }

        foreach ($request->allFiles()['files'] as $file) {
            $asset = Asset::create([
                'user_id'   => $customer->id,
                'folder_id' => $request->get('folder'),
                'author_id' => Auth::user()->id,
                'team_id'   => current_team()->id,
            ]);

            try {
                $asset->addMedia($file)
                    ->toMediaCollection('assets');
            } catch (Exception $e) {
                $asset->delete();
            }
        }

        session()->flash('message', __('File(s) successfully uploaded'));

        return redirect()->back();
    }

    public function destroy(Request $request, User $customer, Asset $file)
    {
        $file->delete();

        session()->flash('message', __('File successfully deleted'));

        return redirect()->back();
    }
}
