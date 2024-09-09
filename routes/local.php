<?php

use App\Exports\BigDataExport;
use App\Models\Review;
use App\Models\Team;
use App\Models\User;
use App\Modules\Team\Scopes\TeamAwareScope;
use App\Notifications\NewFCMNotification;
use App\Notifications\TestNotification;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Spatie\TranslationLoader\LanguageLine;


Route::get('letmein/{id?}', function ($id = null) {
    Auth::loginUsingId($id ?? current_team()->allUsers()->first()->id);

    return redirect()->to('/');
})->name('letmein');

Route::get('test', function () {
    foreach (Review::withoutGlobalScope(TeamAwareScope::class)->get() as $review) {
        $review->update([
            'closes_at' => $review->closes_at->addSecond(1),
        ]);
        $review->update([
            'closes_at' => $review->closes_at->subSecond(1),
        ]);
    }
//    dd(123);
//
//    $a = Crypt::decryptString('eyJpdiI6IktoL0QzSHVJcC9UYUh3UWNBaXFNRUE9PSIsInZhbHVlIjoiMlVOQlNzMFBBSzB2Y1F0Zi9JNnJ0YXNnNHlOZmpGV3BhZ0VEY2lTYzVkK2lWaG1lMUFERFZPeXJHR0YrVGhFaGlMZXM0UG5mdGpXS29pSmhvMGE4MTZRYlFUeFIrYS80NjRZUkZrUllTVDFFTGVhQ1dnb3V1YTRpQ0JsK3pGL20wSjdmSkY5NzZib1kyT2xlSWlEL0pZeG9xK2RNRGtSVUNOYlBQWkhzakJEWDlOdDFvQkJ2T1Y4UTdFT0p6cTdBdE5SU3NqUEpyU3VURzBqNVJYRWNaMFlUMENRb3VOVjRMeDlHUzB4Q3JRc29LQ0ZhaDQva0R3MmRJWnBrTVlGK0UvNWRzRFJ3d1pUWU5GRDZHUEk5VTBVY0xHSjI4Zk1sc2wzT1E0c1VOMEtzVG5mWDB3T2Zqd1RmZjZKUXQzMVhNbjY2Vit0SmpjL2FHaHFoSkZtWEhyQXBwc3luVWozZ3BvVjkrdjdRY1FZZmMyTzdWOFREMXcrdnlmZ1ZjVk1zbGxhVG5HcDdXN2Uxd2N5N3Z6bURMR0FyN1F3RkNXODVYUUo2YzFkYXQyVThwNnNRQWlIQmMzd1VpNmE3eTBrVlUrTU5WYXZUc04wYnZZK2tySU9hZkptMk91Q2ZVbFl6WnVqSGZia1Ezek9HVDEvajFvUVNac0VJdkJvMVdVNVhucWlLbXFWc1BIeU53a3did3VTSzJnPT0iLCJtYWMiOiJkYTljMDFiNDM2YmEyODI1N2JiZTY5ODY3Yzc4ODg0ZmQzYjJlNjNmNzBmN2UxMTMwZWQ4NmRlNGY3NWJhZjVjIn0=');
//    dd(collect(json_decode($a)));
});

Route::group(['middleware' => ['team']], function () {
    Route::get('mail', function () {
        current_team()->allUsers()->each(function (User $user) {
            $user->notify(new TestNotification());
        });
        return view('welcome');
    });
});

Route::get('time', function () {
    Team::all()->each(function (Team $team) {
        $team->users->each(function (User $user) use ($team) {
            dd($user->getSettings($team)->timezone_offset_days);
            if ($user->getSettings($team)->use_own_timezone) {
                $user->save();
            }
        });
    });
});

Route::get('notification', function () {
    $user = User::find(1);
    $user->notify(new TestNotification(current_team()));
});

Route::get('new-notification', function () {
    $user = User::find(1);
    $user->notify(new NewFCMNotification(current_team()));
    return true;
});


Route::get('decrypt', function () {
    dd(Crypt::decryptString('eyJpdiI6InFvNEg3ZThPajhtb2xSWlpzVElIY1E9PSIsInZhbHVlIjoibks0QlhjejV4OWR4dDFOYTM1OXJJSUZIQVRCVFpwM1E4dmkzTXlGL1RGUTg4MUFYYXZuZEUzVXppU014SzcrWGFrQXNkUVdJMW1WWFFSTSt3SSt6N1h0Q1BWK09tbGdCNTJxQ1JoMnhZbXM5Zng2ZjlpQUY3Q21NZ1ZHRWZhRGw4aGFsQ3ZranFFL01IMkpCR0I1dk5SMFBIRVBzbUltZEpsNnlyTVFHMHRXajRXZWJ1cTVIY1Y1eDQxaFNpSGtkVkZEMVdQemU2ZldGZk5sSzNXT2xqSWNRMnZEajRDZmRjNTNjYnc4ODVkbDFVeDYrSm1uT3owb2JjMmswdExtaVV4ME5qTnErU0xEY1ZPaWVHQUZQS2M2TXhKTEQ0MjA4TW1mays5Zm8zU0hPVnEzQlNDWkxqYW9yalF6ZHNLdmNmVGszQUJOSzBiNXZQU1dBT3BiTjBHQVdqT29GVEFWNzVBS3I5VlBnbm0xd3BRdFNjVy9BRGFHRDBaeXFhQmZnWDdKbzVyUmtjbTVkT29hRXoza3Fla2djNUxqVmNtbHF6UDZTbEhNU0VDMmFEcVArVW1lc0NaS3F0emNObVBKdWcxZnNOUDVPcU1PaDR1Smc4Mi9FMzNIUHBXMWx0bnpjMlFhWmJkcFNOSVNEUjkyaUlwcUZSQjBqWUFreUVIcDdaMFR1RGsvVXRjTzA2akJiaU15Q2RNSUpKaS9aamF6YmE3K2FmSnUrUHVkeW5tOVZ1OFJYVkxuaFR1RjRQbnFtdjFFbld4OHpRNkFOQXlYb24rYlhWcW8zSmVXV1hrKytRLzhScHBIeEdwWT0iLCJtYWMiOiIwN2QwMjAyNDgwMzY1Nzc1OGViYzNkYWZiOWQxYmJjZGI1Yjk0Y2NlYTFiNzc5OTRkMjdjNzdkZjkzYTA2M2FiIiwidGFnIjoiIn0='));
});

Route::get('decodee', function () {
    dd(JWT::decode('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJjbGllbnQiOnsiaWQiOjEsImZpcnN0X25hbWUiOiJDcmVhdGl2ZW9yYW5nZSIsImxhc3RfbmFtZSI6IkIuVi4iLCJlbWFpbCI6ImluZm9AY3JlYXRpdmVvcmFuZ2UubmwiLCJlbWFpbF92ZXJpZmllZF9hdCI6bnVsbCwiY291bnRyeSI6bnVsbCwibG9jYWxlIjoiZW4iLCJ0aW1lem9uZSI6IkV1cm9wZVwvQW1zdGVyZGFtIiwiZGF0ZV9mb3JtYXQiOiJsaXR0bGUtZW5kaWFufGRhc2giLCJsYXN0X2xvZ2luIjoiMjAyMS0xMC0yMiAxMToxMDoyNCIsImxhc3RfYWN0aXZpdHkiOiIyMDIxLTEwLTIyIDExOjE0OjQ4IiwiY3VycmVudF90ZWFtX2lkIjoiMSIsImNyZWF0ZWRfYXQiOiIyMDE2LTA5LTAyVDA5OjI1OjI1LjAwMDAwMFoiLCJ1cGRhdGVkX2F0IjoiMjAyMS0xMC0yNlQwODo1Njo0OC4wMDAwMDBaIiwiZGVsZXRlZF9hdCI6bnVsbH0sInllYXIiOiIyMDIxIiwid2VlayI6IjQzIiwiZGF5cyI6WyIyMDIxLTEwLTI1VDAwOjAwOjAwLjAwMDAwMFoiLCIyMDIxLTEwLTI2VDAwOjAwOjAwLjAwMDAwMFoiLCIyMDIxLTEwLTI3VDAwOjAwOjAwLjAwMDAwMFoiLCIyMDIxLTEwLTI4VDAwOjAwOjAwLjAwMDAwMFoiLCIyMDIxLTEwLTI5VDIzOjU5OjU5Ljk5OTk5OVoiXSwiZGF5c19wZXJfd2VlayI6NSwidW5pcXVlX2RheXMiOjAsIm92ZXJhbGxfc2NvcmUiOjAsIm9uX3RhcmdldCI6MSwiZGFpbHlfc3VtbWFyeSI6W10sIndlZWtseV90aW1lbGluZSI6eyIyMDIwMzgiOnsiZGF0ZSI6IjIwMjAtMDktMTRUMTQ6NTY6MDEuNzM2MTczWiIsInNjb3JlIjoxMDAsIm5vV2Vla1Jlc3BvbnNlcyI6MSwibW92aW5nQXZlcmFnZSI6MTAwLCJtb3ZpbmdBdmVyYWdlQ29tcGxldGUiOmZhbHNlfX0sInRvcHNBbmREaXBzIjpbXX0.y4ZGAr3p_IemChs_ery0HpanZCUFUCJYu5H1bM24bYw',
        md5('e92d5e00c6a955837c286bc1198d9801')));
});


Route::get('languages', function () {
    $json = json_decode(file_get_contents(lang_path('en.json')));
    foreach ($json as $key => $value) {
        LanguageLine::create([
            'group' => '*',
            'key'   => $key,
            'text'  => ['en' => $value],
        ]);
    }
    dd($json);
});

Route::get('export/bigdata', function () {
    (new BigDataExport(2021))->queue('big_data.xlsx');
    return 'done';
});


Route::get('user', function () {
    $user = User::find(82);
    dd($user);
});
