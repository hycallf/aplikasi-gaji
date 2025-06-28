<?

public function handle(Request $request, Closure $next, string ...$guards): Response
{
    $guards = empty($guards) ? [null] : $guards;

    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
            // --- LOGIKA BARU DITAMBAHKAN DI SINI ---
            $user = Auth::user();
            if ($user->role === 'operator') {
                return redirect(RouteServiceProvider::HOME); // Operator ke /dashboard
            } else {
                return redirect()->route('user.dashboard'); // Karyawan/Dosen ke dashboard mereka
            }
            // --- AKHIR LOGIKA BARU ---
        }
    }

    return $next($request);
}