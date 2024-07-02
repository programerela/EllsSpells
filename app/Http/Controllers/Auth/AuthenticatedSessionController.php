<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Theme;
use Illuminate\Support\Facades\DB;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        //return redirect()->intended(RouteServiceProvider::HOME);
        return redirect('theme');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function myThemes()
    {
        if (!auth()->user()) {
            return redirect()->route('login');
        }

        $user = User::find(auth()->user()->getAuthIdentifier());

        $themes = $user->themes()->where('approve_status', 'APPROVED')->get();

        return view('user.themes', ['themes' => $themes]);
    }

    public function myDiscussions()
    {
        if (!auth()->user()) {
            return redirect()->route('login');
        }

        $user = User::find(auth()->user()->getAuthIdentifier());

        $discussions = $user->discussions;

        return view('user.discussions', ['discussions' => $discussions]);
    }

    public function followed() {

        $this->authorize('viewAny', Theme::class);

        //$themes = Theme::where('approve_status', 'APPROVED')->get();
        
        if (auth()->check()) {
            $user = User::find(auth()->user() !== null && auth()->user()->getAuthIdentifier());
            $followedThemesFromWhichUserIsNotBlocked = $user->followedThemes() //->get();
                ->whereDoesntHave('blockedUsers', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->get();
        }

        dd($followedThemesFromWhichUserIsNotBlocked);

        $followedThemes = DB::table('themes')
        ->join('theme_user', 'themes.id', '=', 'theme_user.theme_id')
        ->where('theme_user.user_id', auth()->id())
        ->get();

        $followedThemes = Theme::hydrate($followedThemes->toArray());
        
        return view('theme.followed', ['followedThemes' => $followedThemes ?? []]);
    }
}
