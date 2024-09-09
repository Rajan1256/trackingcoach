<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function array_key_exists;
use function dd;

class TestsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Test::class, 'test');
    }

    public function index(Request $request, User $customer)
    {
        $tests = collect(config('trackingcoach.tests'))->map(fn($c) => new $c);

        return view('customers.tests.index', [
            'customer' => $customer,
            'tests'    => $tests,
        ]);
    }

    public function create(Request $request, User $customer)
    {
        $test = intval($request->get('type'));
        $testList = config('trackingcoach.tests');

        if (!array_key_exists($test, $testList)) {
            abort(404);
        }

        $test = new $testList[$test];

        return view('customers.tests.create', [
            'customer' => $customer,
            'test'     => $test,
            'model'    => new Test(),
        ]);
    }

    public function store(Request $request, User $customer)
    {
        $type = $request->get('type');
        $testObject = new $type;
        $this->validate($request, $testObject->getValidationRules());

        $testObject->set($request->only($testObject->getProperties()));
        $model = (new Test)->fill([
            'data' => $testObject,
            'date' => Carbon::createFromFormat('Y-m-d', $request->get('date', Carbon::now()->format('Y-m-d'))),
            'type' => get_class($testObject),
        ]);
        $model->user()->associate($customer);
        $model->author()->associate(Auth::user());
        $model->team()->associate(current_team());
        $model->save();

        foreach ($testObject->getUploadCollections() as $collectionName) {
            if ($request->file($collectionName)) {
                $model->addMedia($request->file($collectionName))
                    ->toMediaCollection($collectionName);
            }
        }

        return redirect()->to(route('customers.tests.show', [
            'customer' => $customer,
            'test'     => $model,
        ]));
    }

    public function edit(Request $request, User $customer, Test $test)
    {
        $model = $test;
        $test = $test->data;

        return view('customers.tests.edit', [
            'customer' => $customer,
            'test'     => $test,
            'model'    => $model,
        ]);
    }

    public function show(Request $request, User $customer, Test $test)
    {
        return view('customers.tests.show', [
            'customer' => $customer,
            'test'     => $test,
        ]);
    }

    public function update(Request $request, User $customer, Test $test)
    {
        $model = $test;
        $test = $test->data;

        $this->validate($request, $test->getValidationRules());

        $test->set($request->only($test->getProperties()));

        $model = $model->fill([
            'data' => $test,
            'date' => Carbon::createFromFormat('Y-m-d', $request->get('date', Carbon::now()->format('d-m-Y'))),
        ]);
        $model->save();

        foreach ($test->getUploadCollections() as $collectionName) {
            if ($request->file($collectionName)) {
                $currentMedia = $model->getMedia($collectionName)->all();

                foreach ($currentMedia as $existing) {
                    $model->deleteMedia($existing->id);
                }

                $model->addMedia($request->file($collectionName))
                    ->toMediaCollection($collectionName);
            }
        }

        return redirect()->to(route('customers.tests.show', [
            'customer' => $customer,
            'test'     => $model,
        ]));
    }

    public function destroy(Request $request, User $customer, Test $test)
    {
        $test->delete();

        session()->flash('message', __('Test successfully deleted'));

        return redirect()->to(route('customers.tests.index', [$customer]));
    }
}
