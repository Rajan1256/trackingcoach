<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use App\Exports\BigDataExport;
use App\Jobs\FinishBigDataExport;
use App\Jobs\StartBigDataExport;
use App\Models\Export;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function current_team;

class ExportsController extends Controller
{
    public function create(Request $request)
    {
        abort_if(!Auth::user()->hasCurrentTeamRole([Roles::ADMIN]), 403);

        $customers = current_team()->users()
            ->isCustomer()
            ->get();

        return view('exports.create', [
            'customers' => $customers,
        ]);
    }

    public function index(Request $request)
    {
        abort_if(!Auth::user()->hasCurrentTeamRole([Roles::ADMIN]), 403);

        $exports = Export::orderBy('created_at', 'desc')->paginate(15);

        return view('exports.index', [
            'exports' => $exports,
        ]);
    }

    public function show(Request $request, Export $export)
    {
        abort_if(!Auth::user()->hasCurrentTeamRole([Roles::ADMIN]), 403);

        $actualFile = $export->file;

        return Storage::disk($actualFile->disk)
            ->download($actualFile->id.'/'.$actualFile->file_name,
                'export_'.$actualFile->created_at->format('d_m_Y').(($export->user_id) ? '_'.Str::snake($export->user->name) : '').'.xlsx');
    }

    public function store(Request $request)
    {
        abort_if(!Auth::user()->hasCurrentTeamRole([Roles::ADMIN]), 403);

        $user = User::find($request->get('customer'));

        abort_if(!$user && !empty($request->get('customer')), 403);

        $export = Export::create([
            'created_by' => Auth::user()->id,
            'user_id'    => $user?->id,
            'team_id'    => current_team()->id,
            'type'       => BigDataExport::class,
            'year'       => $request->get('year'),
            'status'     => 0,
            'data'       => [],
        ]);

        Bus::chain([
            new StartBigDataExport(current_team(), $request->get('year'), $user, $export),
            new FinishBigDataExport(current_team(), $export, ($request->get('mail') == 1), Auth::user()),
        ])->dispatch();

        return redirect()->to(route('exports.index'));
    }
}
