<?php

namespace App\Http\Controllers;

use App\Actions\CreateUserAction;
use App\Actions\StoreSpaceInSessionAction;
use App\Actions\SendVerificationMailAction;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;

use App\Repositories\LoginAttemptRepository;
use App\Repositories\SpaceRepository;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    private $spaceRepository;
    private $loginAttemptRepository;
    private $createUserAction;
    private $sendVerificationMailAction;
    private $storeSpaceInSessionAction;

    public function __construct(
        SpaceRepository $spaceRepository,
        LoginAttemptRepository $loginAttemptRepository,
        CreateUserAction $createUserAction,
        SendVerificationMailAction $sendVerificationMailAction,
        StoreSpaceInSessionAction $storeSpaceInSessionAction
    ) {
        $this->spaceRepository = $spaceRepository;
        $this->loginAttemptRepository = $loginAttemptRepository;
        $this->createUserAction = $createUserAction;
        $this->sendVerificationMailAction = $sendVerificationMailAction;
        $this->storeSpaceInSessionAction = $storeSpaceInSessionAction;
    }

    public function index()
    {
        if (config('app.disable_registration')) {
            abort(404);
        }

        return view('register', [
            'currencies' => Currency::orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        if (config('app.disable_registration')) {
            abort(404);
        }

        $request->validate(User::getValidationRulesForRegistration());

        $user = $this->createUserAction->execute($request->name, $request->email, $request->password);
        $space = $this->spaceRepository->create($request->currency, $user->name . '\'s Space');
        $user->spaces()->attach($space->id, ['role' => 'admin']);

        $this->sendVerificationMailAction->execute($user->id);

        Auth::loginUsingId($user->id);

        $this->loginAttemptRepository->create($user->id, $request->ip(), false);

        $this->storeSpaceInSessionAction->execute($user->spaces[0]->id);

        return redirect()
            ->route('dashboard');
    }
}
