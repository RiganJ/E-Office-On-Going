<?php
namespace App\Http\Controllers\Auth; use App\Http\Controllers\Controller; use App\Services\ActivityLogService; use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth;
class AuthenticatedSessionController extends Controller
{
    public function create(Request $request)
    {
        $this->makeCaptcha($request);

        return view('auth.login');
    }

    public function refreshCaptcha(Request $request)
    {
        $this->makeCaptcha($request, true);

        return back()->withInput();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'integer'],
        ]);

        $expiresAt = $request->session()->get('login_captcha_expires_at', 0);
        $answer = $request->session()->get('login_captcha_answer');

        if ($expiresAt < now()->timestamp || $answer === null || ! hash_equals((string) $answer, (string) $data['captcha'])) {
            $this->makeCaptcha($request, true);

            return back()->withErrors(['captcha' => 'Jawaban CAPTCHA tidak tepat atau sudah kedaluwarsa.'])->onlyInput('email');
        }

        $request->session()->forget(['login_captcha_answer', 'login_captcha_question', 'login_captcha_expires_at']);

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $request->boolean('remember'))) {
            $this->makeCaptcha($request, true);

            return back()->withErrors(['email' => 'Email atau kata sandi tidak valid.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        ActivityLogService::record($request->user(), 'LOGIN');

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        ActivityLogService::record($request->user(), 'LOGOUT');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function makeCaptcha(Request $request, bool $force = false): void
    {
        if (! $force && $request->session()->has('login_captcha_question') && $request->session()->get('login_captcha_expires_at', 0) >= now()->timestamp) {
            return;
        }

        $first = random_int(2, 9);
        $second = random_int(1, 9);
        $request->session()->put([
            'login_captcha_answer' => $first + $second,
            'login_captcha_question' => "{$first} + {$second} = ?",
            'login_captcha_expires_at' => now()->addMinutes(5)->timestamp,
        ]);
    }
}
