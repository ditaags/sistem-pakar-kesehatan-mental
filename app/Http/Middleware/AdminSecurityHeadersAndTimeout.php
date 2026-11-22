<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AdminSecurityHeadersAndTimeout
{
    /**
     * Waktu idle maksimum dalam detik (15 menit = 900 detik).
     */
    protected int $idleTimeout = 15 * 60;

    /**
     * Handle an incoming request.
     *
     * 1. Memeriksa idle timeout sesi admin (15 menit tanpa aktivitas).
     * 2. Menambahkan header no-store pada response agar tombol Back browser
     *    tidak menampilkan halaman admin dari cache setelah logout.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Periksa batas waktu idle untuk admin yang terautentikasi
        if (Auth::check()) {
            $lastActivity = $request->session()->get('admin_last_activity');
            $currentTime = now()->timestamp;

            if ($lastActivity && ($currentTime - $lastActivity > $this->idleTimeout)) {
                Auth::logout();

                if ($recaller = Auth::getRecallerName()) {
                    Cookie::queue(Cookie::forget($recaller));
                }

                $request->session()->forget('admin_last_activity');
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Sesi berakhir, silakan masuk lagi.');
            }

            // Perbarui waktu aktivitas terakhir
            $request->session()->put('admin_last_activity', $currentTime);
        }

        // 2. Jalankan request berikutnya
        $response = $next($request);

        // 3. Tambahkan header no-store anti-cache pada area admin
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
