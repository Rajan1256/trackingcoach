<?php

use App\Http\Controllers\AcceptConsentController;
use App\Http\Controllers\AcceptInviteController;
use App\Http\Controllers\ActivateConsentController;
use App\Http\Controllers\AssignToTeamController;
use App\Http\Controllers\BillingPortalController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\ConsentsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Customers\CopyTemplateQuestionController;
use App\Http\Controllers\Customers\CopyTemplateReviewQuestionsController;
use App\Http\Controllers\Customers\DailyQuestionsController;
use App\Http\Controllers\Customers\DailyQuestionsTemplatesController;
use App\Http\Controllers\Customers\FilesController;
use App\Http\Controllers\Customers\FoldersController;
use App\Http\Controllers\Customers\GoalsController;
use App\Http\Controllers\Customers\HistoryController;
use App\Http\Controllers\Customers\InterviewsController;
use App\Http\Controllers\Customers\MonthlyReportController;
use App\Http\Controllers\Customers\NotesController;
use App\Http\Controllers\Customers\ReviewQuestionsController;
use App\Http\Controllers\Customers\ReviewQuestionsTemplatesController;
use App\Http\Controllers\Customers\ReviewsController;
use App\Http\Controllers\Customers\ReviewSupporterResultController;
use App\Http\Controllers\Customers\SendInviteController;
use App\Http\Controllers\Customers\SupportersController;
use App\Http\Controllers\Customers\TestsController;
use App\Http\Controllers\Customers\TestsMediaController;
use App\Http\Controllers\Customers\TimeoutsController;
use App\Http\Controllers\Customers\VerbatimController;
use App\Http\Controllers\Customers\WeeklyReportController;
use App\Http\Controllers\CustomersAppController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeactivateConsentController;
use App\Http\Controllers\DestroyTeamPermanentlyController;
use App\Http\Controllers\DownloadConsentAttachmentController;
use App\Http\Controllers\ExportsController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\InviteOverviewController;
use App\Http\Controllers\PairAppController;
use App\Http\Controllers\PromoteTeamMemberController;
use App\Http\Controllers\ResendVerifyController;
use App\Http\Controllers\RestoreTeamController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TranslationsController;
use App\Http\Controllers\UnassignFromTeamController;
use App\Http\Controllers\VerifyController;
use App\Http\Livewire\Customers;
use App\Http\Livewire\FAQ;
use App\Http\Livewire\ProgramOverview;
use App\Http\Livewire\Questionnaire;
use App\Http\Livewire\QuestionnaireTemplates;
use App\Http\Livewire\ReviewQuestionnaireTemplates;
use App\Http\Livewire\Translations;
use App\Http\Middleware\CheckForUnacceptedConsents;
use App\Http\Middleware\EnsureUserHasAccessToUser;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', 'dashboard');


require __DIR__.'/auth.php';

Route::group(['middleware' => 'no-team-access'], function () {
    Route::get('/no-teams', function () {
        abort(401);
    })->name('no-teams');
});

Route::prefix('verify')->group(function () {
    Route::get('', [VerifyController::class, 'index'])
        ->name('verify.index');
    Route::get('resend', ResendVerifyController::class)
        ->name('verify.resend');
    Route::get('{verifyUser:token}', [VerifyController::class, 'show'])
        ->name('verify.show');
});

Route::prefix('select-team')->group(function () {
    Route::get('', DashboardController::class)
        ->name('select-team');
});

Route::group(['middleware' => ['team']], function () {
    Route::group(['middleware' => ['auth', 'team-access', 'check-consents', 'is-verified']], function () {
        Route::get('dashboard', function (Illuminate\Http\Request $request) {
            return (new CustomerController())->show($request, Auth::user());
        })->name('dashboard');

        Route::prefix('/customers')->group(function () {
            Route::get('/', Customers::class)
                ->name('customers');
            Route::get('/archived', Customers::class)
                ->name('customers.archived');
            Route::get('/create', [CustomerController::class, 'create'])
                ->name('customers.create');

            Route::post('/', [CustomerController::class, 'store'])
                ->name('customers.store');

            Route::prefix('/{customer}')->middleware(EnsureUserHasAccessToUser::class)->group(function () {
                Route::get('/', [CustomerController::class, 'show'])
                    ->name('customers.show');
                Route::get('/edit', [CustomerController::class, 'edit'])
                    ->name('customers.edit');

                Route::put('/', [CustomerController::class, 'update'])
                    ->name('customers.update');

                Route::delete('/', [CustomerController::class, 'destroy'])
                    ->name('customers.destroy');

                Route::prefix('/daily-questions')->group(function () {
                    Route::get('/', Questionnaire::class)
                        ->name('customers.daily-questions.index');
                    Route::post('/', [DailyQuestionsController::class, 'store'])
                        ->name('customers.daily-questions.store');

                    Route::prefix('templates')->group(function () {
                        Route::get('/', QuestionnaireTemplates::class)
                            ->name('customers.daily-questions.templates.index');
                        Route::post('/', [DailyQuestionsTemplatesController::class, 'store'])
                            ->name('customers.daily-questions.templates.store');

                        Route::prefix('/{question}')->group(function () {
                            Route::put('/', [DailyQuestionsTemplatesController::class, 'update'])
                                ->name('customers.daily-questions.templates.update');
                            Route::delete('/', [DailyQuestionsTemplatesController::class, 'destroy'])
                                ->name('customers.daily-questions.templates.destroy');
                        });
                    });

                    Route::prefix('/{question}')->group(function () {
                        Route::put('/', [DailyQuestionsController::class, 'update'])
                            ->name('customers.daily-questions.update');
                        Route::delete('/', [DailyQuestionsController::class, 'destroy'])
                            ->name('customers.daily-questions.destroy');

                        Route::post('/', CopyTemplateQuestionController::class)
                            ->name('customers.daily-questions.template');
                    });
                });

                Route::prefix('/files')->group(function () {
                    Route::get('/', [FilesController::class, 'index'])
                        ->name('customers.files.index');
                    Route::post('/', [FilesController::class, 'store'])
                        ->name('customers.files.store');

                    Route::prefix('/{file}')->group(function () {
                        Route::get('/', [FilesController::class, 'show'])
                            ->name('customers.files.show');

                        Route::delete('/', [FilesController::class, 'destroy'])
                            ->name('customers.files.destroy');
                    });
                });

                Route::prefix('/folders')->group(function () {
                    Route::post('/', [FoldersController::class, 'store'])
                        ->name('customers.folders.store');

                    Route::prefix('/{folder}')->group(function () {
                        Route::delete('/', [FoldersController::class, 'destroy'])
                            ->name('customers.folders.destroy');
                    });
                });

                Route::prefix('/history')->group(function () {
                    Route::get('/', [HistoryController::class, 'index'])
                        ->name('customers.history.index');

                    Route::prefix('/{date}')->group(function () {
                        Route::get('/', [HistoryController::class, 'show'])
                            ->name('customers.history.show');
                    });
                });

                Route::prefix('/interviews')->group(function () {
                    Route::get('/', [InterviewsController::class, 'index'])
                        ->name('customers.interviews.index');
                    Route::get('/create', [InterviewsController::class, 'create'])
                        ->name('customers.interviews.create');

                    Route::post('/', [InterviewsController::class, 'store'])
                        ->name('customers.interviews.store');

                    Route::prefix('/{interview}')->group(function () {
                        Route::get('/', [InterviewsController::class, 'show'])
                            ->name('customers.interviews.show');
                        Route::get('/edit', [InterviewsController::class, 'edit'])
                            ->name('customers.interviews.edit');

                        Route::put('/', [InterviewsController::class, 'update'])
                            ->name('customers.interviews.update');

                        Route::delete('/', [InterviewsController::class, 'destroy'])
                            ->name('customers.interviews.destroy');
                    });
                });

                Route::prefix('/timeouts')->group(function () {
                    Route::get('/', [TimeoutsController::class, 'index'])
                        ->name('customers.timeouts.index');
                    Route::get('/create', [TimeoutsController::class, 'create'])
                        ->name('customers.timeouts.create');

                    Route::post('/', [TimeoutsController::class, 'store'])
                        ->name('customers.timeouts.store');

                    Route::prefix('/{timeout}')->group(function () {
                        Route::get('/edit', [TimeoutsController::class, 'edit'])
                            ->name('customers.timeouts.edit');

                        Route::put('/', [TimeoutsController::class, 'update'])
                            ->name('customers.timeouts.update');

                        Route::delete('/', [TimeoutsController::class, 'destroy'])
                            ->name('customers.timeouts.destroy');
                    });
                });

                Route::prefix('/reviews')->group(function () {
                    Route::get('/', [ReviewsController::class, 'index'])
                        ->name('customers.reviews.index');
                    Route::get('/create', [ReviewsController::class, 'create'])
                        ->name('customers.reviews.create');

                    Route::post('/', [ReviewsController::class, 'store'])
                        ->name('customers.reviews.store');

                    Route::prefix('/{review}')->group(function () {
                        Route::get('/', [ReviewsController::class, 'show'])
                            ->name('customers.reviews.show');
                        Route::get('/edit', [ReviewsController::class, 'edit'])
                            ->name('customers.reviews.edit');

                        Route::put('/', [ReviewsController::class, 'update'])
                            ->name('customers.reviews.update');

                        Route::delete('/', [ReviewsController::class, 'destroy'])
                            ->name('customers.reviews.destroy');

                        Route::get('/results/{supporter}', ReviewSupporterResultController::class)
                            ->name('customers.reviews.detail');

                        Route::post('/send-invitation/{supporter}', SendInviteController::class)
                            ->name('customers.reviews.sendInvitation');
                    });

                    Route::prefix('/questions')->group(function () {
                        Route::post('/', [ReviewQuestionsController::class, 'store'])
                            ->name('customers.reviews.questions.store');

                        Route::prefix('/{question}')->group(function () {
                            Route::put('/', [ReviewQuestionsController::class, 'update'])
                                ->name('customers.reviews.questions.update');
                            Route::delete('/', [ReviewQuestionsController::class, 'destroy'])
                                ->name('customers.reviews.questions.destroy');

                            Route::post('/', CopyTemplateReviewQuestionsController::class)
                                ->name('customers.reviews.questions.template');
                        });

                        Route::prefix('templates')->group(function () {
                            Route::get('/', ReviewQuestionnaireTemplates::class)
                                ->name('customers.reviews.questions.templates.index');
                            Route::post('/', [ReviewQuestionsTemplatesController::class, 'store'])
                                ->name('customers.reviews.questions.templates.store');

                            Route::prefix('/{question}')->group(function () {
                                Route::put('/', [ReviewQuestionsTemplatesController::class, 'update'])
                                    ->name('customers.reviews.questions.templates.update');
                                Route::delete('/', [ReviewQuestionsTemplatesController::class, 'destroy'])
                                    ->name('customers.reviews.questions.templates.destroy');
                            });
                        });
                    });
                });

                Route::prefix('/supporters')->group(function () {
                    Route::get('/create', [SupportersController::class, 'create'])
                        ->name('customers.supporters.create');

                    Route::post('/', [SupportersController::class, 'store'])
                        ->name('customers.supporters.store');

                    Route::prefix('/{supporter}')->group(function () {
                        Route::get('/edit', [SupportersController::class, 'edit'])
                            ->name('customers.supporters.edit');

                        Route::put('/', [SupportersController::class, 'update'])
                            ->name('customers.supporters.update');

                        Route::delete('/', [SupportersController::class, 'destroy'])
                            ->name('customers.supporters.destroy');
                    });
                });

                Route::prefix('/goals')->group(function () {
                    Route::get('/create', [GoalsController::class, 'create'])
                        ->name('customers.goals.create');

                    Route::post('/', [GoalsController::class, 'store'])
                        ->name('customers.goals.store');

                    Route::prefix('/{goal}')->group(function () {
                        Route::get('/edit', [GoalsController::class, 'edit'])
                            ->name('customers.goals.edit');

                        Route::put('/', [GoalsController::class, 'update'])
                            ->name('customers.goals.update');

                        Route::delete('/', [GoalsController::class, 'destroy'])
                            ->name('customers.goals.destroy');
                    });
                });

                Route::prefix('/notes')->group(function () {
                    Route::get('/', [NotesController::class, 'index'])
                        ->name('customers.notes.index');
                    Route::get('/create', [NotesController::class, 'create'])
                        ->name('customers.notes.create');

                    Route::post('/', [NotesController::class, 'store'])
                        ->name('customers.notes.store');

                    Route::prefix('/{note}')->group(function () {
                        Route::get('/edit', [NotesController::class, 'edit'])
                            ->name('customers.notes.edit');
                        Route::put('/', [NotesController::class, 'update'])
                            ->name('customers.notes.update');

                        Route::delete('/', [NotesController::class, 'destroy'])
                            ->name('customers.notes.destroy');
                    });
                });

                Route::prefix('/tests')->group(function () {
                    Route::get('/', [TestsController::class, 'index'])
                        ->name('customers.tests.index');
                    Route::get('/create', [TestsController::class, 'create'])
                        ->name('customers.tests.create');

                    Route::post('/', [TestsController::class, 'store'])
                        ->name('customers.tests.store');

                    Route::prefix('/{test}')->group(function () {
                        Route::get('/', [TestsController::class, 'show'])
                            ->name('customers.tests.show');
                        Route::get('/edit', [TestsController::class, 'edit'])
                            ->name('customers.tests.edit');

                        Route::put('/', [TestsController::class, 'update'])
                            ->name('customers.tests.update');

                        Route::delete('/', [TestsController::class, 'destroy'])
                            ->name('customers.tests.destroy');

                        Route::get('/media/{media}', TestsMediaController::class)
                            ->name('customers.tests.media');
                    });
                });

                Route::prefix('/verbatim')->group(function () {
                    Route::get('/', [VerbatimController::class, 'index'])
                        ->name('customers.verbatim.index');
                });

                Route::prefix('/program-overview')->group(function () {
                    Route::get('/', ProgramOverview::class)
                        ->name('customers.program-overview.index');
                });

                Route::get('/month/{year}/{month}', MonthlyReportController::class)
                    ->name('reports.monthly');
                Route::get('/week/{year}/{week}', WeeklyReportController::class)
                    ->name('reports.weekly');
            });
        });

        Route::prefix('/translations')->group(function () {
            Route::get('/', Translations::class)
                ->name('translations.index');

            Route::prefix('/{line}')->group(function () {
                Route::put('/', [TranslationsController::class, 'update'])
                    ->name('translations.update');

                Route::get('/edit', [TranslationsController::class, 'edit'])
                    ->name('translations.edit');
            });
        });

        Route::prefix('billing')->group(function () {
            Route::get('portal', [BillingPortalController::class, 'index'])
                ->name('billing.portal');

            Route::get('stripe-portal', function () {
                current_team()->createOrGetStripeCustomer();
                return current_team()->redirectToBillingPortal(\route('billing.portal'));
            })->name('billing.stripe.portal');

            Route::get('stripe-home', function () {
                return redirect()->to(route('billing.portal'));
            })->name('home');

            Route::get('subscribe/{plan?}', [BillingPortalController::class, 'view'])->name('billing.subscribe');
        });

        Route::prefix('/exports')->group(function () {
            Route::get('/', [ExportsController::class, 'index'])
                ->name('exports.index');
            Route::get('/create', [ExportsController::class, 'create'])
                ->name('exports.create');

            Route::post('/', [ExportsController::class, 'store'])
                ->name('exports.store');

            Route::prefix('/{export}')->group(function () {
                Route::get('/', [ExportsController::class, 'show'])
                    ->name('exports.show');
            });
        });

        Route::prefix('/settings')->group(function () {
            Route::get('/', [SettingsController::class, 'show'])
                ->name('settings.show');
            Route::post('/', [SettingsController::class, 'update'])
                ->name('settings.update');
        });

        Route::prefix('/app')->group(function () {
            Route::get('/pair', PairAppController::class)
                ->name('app.pair');
            Route::get('/customers', [CustomersAppController::class, 'index'])
                ->name('app.customers.index');

            Route::prefix('/{customer}')->group(function () {
                Route::get('download', [CustomersAppController::class, 'show'])
                    ->name('app.customers.show');
            });
        });

        Route::prefix('/faq')->group(function () {
            Route::get('/', FAQ::class)
                ->name('faq');

            Route::prefix('/manage')->group(function () {
                Route::get('/', [FaqController::class, 'index'])
                    ->name('faq.manage.index');
                Route::get('/create', [FaqController::class, 'create'])
                    ->name('faq.manage.create');

                Route::post('/', [FaqController::class, 'store'])
                    ->name('faq.manage.store');

                Route::prefix('/{faq}')->group(function () {
                    Route::get('/edit', [FaqController::class, 'edit'])
                        ->name('faq.manage.edit');

                    Route::put('/', [FaqController::class, 'update'])
                        ->name('faq.manage.update');

                    Route::delete('/', [FaqController::class, 'destroy'])
                        ->name('faq.manage.destroy');
                });
            });
        });

        Route::prefix('/teams')->group(function () {
            Route::get('/', [TeamController::class, 'index'])
                ->name('teams.index')->withTrashed();
            Route::get('/create', [TeamController::class, 'create'])
                ->name('teams.create')->withTrashed();

            Route::post('/', [TeamController::class, 'store'])
                ->name('teams.store')->withTrashed();

            Route::prefix('/members')->group(function () {
                Route::get('/', [TeamMemberController::class, 'index'])
                    ->name('teams.members');
                Route::get('/create', [TeamMemberController::class, 'create'])
                    ->name('teams.members.create');

                Route::post('/', [TeamMemberController::class, 'store'])
                    ->name('teams.members.store');

                Route::prefix('/{member}')->group(function () {
                    Route::get('/', [TeamMemberController::class, 'show'])
                        ->name('teams.members.show');

                    Route::put('/', [TeamMemberController::class, 'update'])
                        ->name('teams.members.update');
                    Route::put('/promote', PromoteTeamMemberController::class)
                        ->name('teams.members.promote');

                    Route::delete('/', [TeamMemberController::class, 'destroy'])
                        ->name('teams.members.destroy');
                });
            });

            Route::prefix('/{team}')->group(function () {
                Route::get('/', [TeamController::class, 'show'])
                    ->name('teams.show')->withTrashed();
                Route::get('/edit', [TeamController::class, 'edit'])
                    ->name('teams.edit')->withTrashed();

                Route::put('/', [TeamController::class, 'update'])
                    ->name('teams.update')->withTrashed();
                Route::put('/restore', RestoreTeamController::class)
                    ->name('teams.restore')->withTrashed();

                Route::delete('/', [TeamController::class, 'destroy'])
                    ->name('teams.destroy')->withTrashed();
                Route::delete('/permanently', DestroyTeamPermanentlyController::class)
                    ->name('teams.permanently')->withTrashed();

                Route::post('/assign', AssignToTeamController::class)
                    ->name('teams.assign');
                Route::delete('/unassign', UnassignFromTeamController::class)
                    ->name('teams.unassign');
            });
        });

        Route::prefix('/timeouts')->group(function () {
            Route::get('/', [\App\Http\Controllers\TimeoutsController::class, 'index'])
                ->name('timeouts.index');
            Route::get('/create', [\App\Http\Controllers\TimeoutsController::class, 'create'])
                ->name('timeouts.create');

            Route::post('/', [\App\Http\Controllers\TimeoutsController::class, 'store'])
                ->name('timeouts.store');

            Route::prefix('/{timeout}')->group(function () {
                Route::get('/edit', [\App\Http\Controllers\TimeoutsController::class, 'edit'])
                    ->name('timeouts.edit');

                Route::put('/', [\App\Http\Controllers\TimeoutsController::class, 'update'])
                    ->name('timeouts.update');

                Route::delete('/', [\App\Http\Controllers\TimeoutsController::class, 'destroy'])
                    ->name('timeouts.destroy');
            });
        });

        Route::prefix('/consents')->group(function () {
            Route::get('/', [ConsentsController::class, 'index'])
                ->name('consents.index');
            Route::get('/create', [ConsentsController::class, 'create'])
                ->name('consents.create');
            Route::post('/', [ConsentsController::class, 'store'])
                ->name('consents.store');

            Route::get('/accept', [AcceptConsentController::class, 'index'])
                ->name('consents.accept.index')
                ->withoutMiddleware(CheckForUnacceptedConsents::class);
            Route::post('/accept', [AcceptConsentController::class, 'store'])
                ->name('consents.accept.store')
                ->withoutMiddleware(CheckForUnacceptedConsents::class);

            Route::prefix('/{consent}')->group(function () {
                Route::get('/', [ConsentsController::class, 'show'])
                    ->name('consents.show');
                Route::get('/edit', [ConsentsController::class, 'edit'])
                    ->name('consents.edit');

                Route::get('/download/{media}', DownloadConsentAttachmentController::class)
                    ->name('consents.download')
                    ->withoutMiddleware(CheckForUnacceptedConsents::class);

                Route::post('/activate', ActivateConsentController::class)
                    ->name('consents.activate');
                Route::post('/deactivate', DeactivateConsentController::class)
                    ->name('consents.deactivate');

                Route::put('/', [ConsentsController::class, 'update'])
                    ->name('consents.update');
                Route::delete('/', [ConsentsController::class, 'destroy'])
                    ->name('consents.destroy');
            });
        });
    });

    Route::prefix('/accept')->group(function () {
        Route::prefix('{token}')->where(['token' => '[A-Za-z0-9]{20}'])->group(function () {
            Route::get('/', [AcceptInviteController::class, 'show'])
                ->name('accept.invite.show');
            Route::post('/', [AcceptInviteController::class, 'store'])
                ->name('accept.invite.store');
        });
    });

    Route::prefix('{code}')->where(['code' => '[A-Za-z0-9]{6}'])->group(function () {
        Route::get('/', [CodeController::class, 'show'])
            ->name('code.show');
        Route::post('/', [CodeController::class, 'store'])
            ->name('code.store');
    });
});

Route::get('/invite-overview/{type}/{date}', InviteOverviewController::class)
    ->name('invite.overview');