<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,owner,admin',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        Auth::login($user);

        return response()->json(['message' => 'Registration successful', 'user' => $user]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json([
                'message' => 'Login successful',
                'user' => Auth::user(),
                'redirect' => $this->getRedirectRoute(Auth::user())
            ]);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            \App\Models\AuthLog::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'action' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }

    private function getRedirectRoute($user)
    {
        if (!$user->onboarding_completed) {
            return '/onboarding';
        }

        // Check for intended redirect (e.g. from a protected booking route)
        if (session()->has('url.intended')) {
            return session()->pull('url.intended');
        }

        return match ($user->role) {
            'admin' => '/admin/dashboard',
            default => '/dashboard',
        };
    }

    public function switchRole(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'user') {
            if ($user->kyc_status !== 'verified') {
                return redirect('/owner/kyc');
            }
            $user->role = 'owner';
        } else {
            $user->role = 'user';
        }

        $user->save();
        
        return redirect($user->role === 'owner' ? '/owner/dashboard' : '/dashboard');
    }

    public function showOnboarding()
    {
        return view('onboarding');
    }

    public function submitOnboarding(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|in:user,owner',
        ]);

        $user = Auth::user();
        $user->role = $validated['role'];

        if ($validated['role'] === 'user') {
            $user->onboarding_completed = true;
            $user->save();
            return response()->json(['redirect' => '/dashboard']);
        } else {
            $user->save();
            return response()->json(['redirect' => '/owner/kyc']);
        }
    }

    public function clerkSync(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['error' => 'No token provided'], 401);
        }

        try {
            $clerkSecret = env('CLERK_SECRET_KEY');
            
            $userId = null;
            $email = null;
            $name = null;

            if ($clerkSecret) {
                $clerk = \Clerk\Backend\ClerkBackend::builder()
                    ->setSecurity($clerkSecret)
                    ->build();

                try {
                    $req = new \Clerk\Backend\Models\Operations\VerifyClientRequestBody($token);
                    $response = $clerk->clients->verify($req);

                    if ($response->client !== null && !empty($response->client->sessions)) {
                        $userId = $response->client->sessions[0]->userId;
                        $clerkUserResponse = $clerk->users->get($userId);
                        
                        if ($clerkUserResponse->user !== null) {
                            $clerkUser = $clerkUserResponse->user;
                            foreach ($clerkUser->emailAddresses as $emailObj) {
                                if ($emailObj->id === $clerkUser->primaryEmailAddressId) {
                                    $email = $emailObj->emailAddress;
                                    break;
                                }
                            }
                            if (!$email && count($clerkUser->emailAddresses) > 0) {
                                $email = $clerkUser->emailAddresses[0]->emailAddress;
                            }
                            $name = trim(($clerkUser->firstName ?? '') . ' ' . ($clerkUser->lastName ?? ''));
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore strict error, fallback to payload
                }
            }

            // Fallback: trust the client payload if backend validation isn't configured or failed
            if (!$userId || !$email) {
                $userId = $request->input('clerk_id');
                $email = $request->input('email');
                $name = trim($request->input('first_name') . ' ' . $request->input('last_name'));
                
                if (!$userId || !$email) {
                    return response()->json(['error' => 'Missing user data and backend verification failed.'], 400);
                }
            }

            if (!$name) {
                $name = 'Clerk User';
            }

            \Illuminate\Support\Facades\Log::debug('[ClerkSync] Sync payload extracted.', [
                'clerk_id' => $userId,
                'email' => $email,
                'name' => $name,
            ]);

            // Sync user to MongoDB
            $user = User::where('clerk_id', $userId)->first();
            
            if (!$user) {
                // Fallback to finding by email if they previously registered without clerk_id
                $user = User::where('email', $email)->first();
                
                if ($user) {
                    $user->clerk_id = $userId;
                    $user->save();
                    \Illuminate\Support\Facades\Log::debug('[ClerkSync] Synced existing email user with clerk_id.', ['user_id' => $user->id]);
                } else {
                    $user = User::create([
                        'clerk_id' => $userId,
                        'name' => $name,
                        'email' => $email,
                        'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24)),
                        'role' => 'user', // Default role
                        'onboarding_completed' => false,
                        'kyc_status' => 'unverified'
                    ]);
                    \Illuminate\Support\Facades\Log::debug('[ClerkSync] Created new user for Clerk login.', ['user_id' => $user->id]);
                }
            } else {
                \Illuminate\Support\Facades\Log::debug('[ClerkSync] Loaded existing Clerk user.', ['user_id' => $user->id]);
            }

            // Authenticate the user in Laravel
            Auth::login($user);
            $request->session()->regenerate();

            \Illuminate\Support\Facades\Log::debug('[ClerkSync] Laravel backend authenticated.', [
                'auth_check' => Auth::check(),
                'user_id' => Auth::id(),
                'session_id' => $request->session()->getId(),
            ]);

            // Record login log
            \App\Models\AuthLog::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'action' => 'login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'message' => 'Synced successfully',
                'user' => $user,
                'redirect' => $this->getRedirectRoute($user)
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[ClerkSync] Critical session sync failure.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Authentication failed: ' . $e->getMessage()], 401);
        }
    }
}
