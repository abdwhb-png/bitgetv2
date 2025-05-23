<?php

namespace App\Providers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use App\Http\Helpers\RequestHelper;
use Illuminate\Support\Facades\Route;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Dynamically bind LoginResponse to customize behavior
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    $isAdminDomain = RequestHelper::isAdminDomain($request);
                    return redirect()->to($isAdminDomain ? '/dashboard' : '/');
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Dynamically resolve views based on the subdomain
        Fortify::loginView(function (Request $request) {
            $isAdminDomain = RequestHelper::isAdminDomain($request);
            $loginView = $isAdminDomain ? 'Auth/Admin/Login' : 'Auth/Application/Login';

            $default = User::role($isAdminDomain ? 'super-admin' : 'user')->first();

            return Inertia::render($loginView, [
                'defaultEmail' => env('APP_ENV') === 'local' ? $default->email : null,
                'canResetPassword' => Route::has('password.request'),
                'status' => session('status'),
            ]);
        });

        Fortify::registerView(function (Request $request) {
            $isAdminDomain = RequestHelper::isAdminDomain($request);
            $registerView = $isAdminDomain ? 'Auth/Admin/Register' : 'Auth/Application/Register';

            return Inertia::render($registerView);
        });

        // Customize redirects
        Fortify::redirects('logout', function (Request $request) {
            return RequestHelper::isAdminDomain($request) ? '/' : '/login';
        });

        // Rate limiter configurations
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower($request->input(Fortify::username())) . '|' . $request->ip()
            );

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
