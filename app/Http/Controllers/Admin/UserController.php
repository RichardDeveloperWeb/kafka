<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Support\Admin\AdminView;
use App\Support\Admin\UserManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(private readonly UserManager $users) {}

    public function index(): Response
    {
        return Inertia::render('admin/users/Index', [
            'users' => User::query()
                ->orderBy('name')
                ->get()
                ->map(fn (User $user): array => AdminView::user($user))
                ->values()
                ->all(),
        ]);
    }

    public function create(): Response
    {
        return $this->form(null);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->users->save($request->validated(), $this->authenticatedUser($request));

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El usuario se creó correctamente.',
        ]);

        return to_route('admin.users.index');
    }

    public function edit(User $user): Response
    {
        return $this->form($user);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->users->save($request->validated(), $this->authenticatedUser($request), $user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El usuario se actualizó correctamente.',
        ]);

        return to_route('admin.users.index');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->users->destroy($user, $this->authenticatedUser($request));

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El usuario se eliminó correctamente.',
        ]);

        return to_route('admin.users.index');
    }

    private function form(?User $user): Response
    {
        return Inertia::render('admin/users/Form', [
            'user' => $user ? AdminView::user($user) : null,
            'roles' => AdminView::roles(),
        ]);
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();

        abort_if(! $user instanceof User, 401);

        return $user;
    }
}
