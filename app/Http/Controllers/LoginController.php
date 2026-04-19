<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\MotdepasseOublieRequest;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class LoginController extends BaseController
{
    public function __construct(protected UserService $userService) {}

     // ========================================================================
    // 🖥️ SHOW LOGIN FORM : Affiche la page de connexion
    // ========================================================================
 public function showLoginForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route('dashboard.index');
        }

        return view('auth.login');
    }



    // ========================================================================
    // 🔐 LOGIN
    // ========================================================================
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $credentials = $request->only('email', 'password');
            $user = User::where('email', $credentials['email'])->first();

            if (!$user || $user->etat !== User::ETAT_ACTIF || !Auth::attempt($credentials)) {
                throw ValidationException::withMessages([
                    'email' => 'Identifiants invalides ou compte suspendu.'
                ]);
            }

            $request->session()->regenerate();
            $this->userService->enregistrerConnexion($user->id);

            return $this->respondSuccess('Connexion réussie.', [
                'user'       => $this->userService->formatUser($user),
                'session_id' => $request->session()->getId()
            ]);
        });
    }

    // ========================================================================
    // 📝 REGISTER
    // ========================================================================
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $data = $request->validated();
            $data['role'] = $data['role'] ?? 'agent'; // Rôle par défaut
            $data['etat'] = User::ETAT_ACTIF;

            $user = $this->userService->creer($data);
            return $this->respondSuccess('Compte créé avec succès.', $this->userService->formatUser($user), 201);
        });
    }

    // ========================================================================
    // 📧 FORGOT PASSWORD
    // ========================================================================
    public function forgotPassword(MotdepasseOublieRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $status = Password::sendResetLink($request->only('email'));

            return $status === Password::RESET_LINK_SENT
                ? $this->respondSuccess('Si cet email est associé à un compte, un lien de réinitialisation vous a été envoyé.')
                : $this->respondError('Impossible d\'envoyer le lien. Veuillez réessayer plus tard.', [], 500);
        });
    }

    // ========================================================================
    // 🔑 RESET PASSWORD
    // ========================================================================
    public function resetPassword(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'token'    => 'required|string',
                'email'    => 'required|email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill(['password' => Hash::make($password)])->save();
                    $user->setRememberToken(Str::random(60));
                    event(new PasswordReset($user));
                }
            );

            return $status === Password::PASSWORD_RESET
                ? $this->respondSuccess('Mot de passe réinitialisé avec succès.')
                : $this->respondError('Le lien de réinitialisation est invalide ou a expiré.', [], 400);
        });
    }

    // ========================================================================
    // 🚪 LOGOUT & 👤 ME
    // ========================================================================
    public function logout(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return $this->respondSuccess('Déconnexion réussie.');
        });
    }

    public function me(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $user = $request->user();
            return $user
                ? $this->respondSuccess('Profil récupéré.', $this->userService->formatUser($user))
                : $this->respondError('Non authentifié.', [], 401);
        });
    }
}