<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserDestroyRequest;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controller responsible for displaying user actions
 *
 * @class UserController
 */
class UserController extends Controller
{
    protected User $userModel;
    protected UserService $userService;

    /**
     * Constructor
     *
     * @param User $user
     * @param UserService $userService
     */
    public function __construct(User $user, UserService $userService)
    {
        $this->userModel   = $user;
        $this->userService = $userService;
    }

    /**
     * User list
     *
     * @return View
     */
    public function index(): View
    {
        $users = $this->userModel->getUsers();

        return view('users.index', compact('users'));
    }

    /**
     * Create user form
     *
     * @return View
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store user
     *
     * @param UserStoreRequest $request
     * @return RedirectResponse
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        $this->userService->create($request->all());

        return redirect()->route('user.index')->with('notification', 'User created successfully.');
    }

    /**
     * Edit user form
     *
     * @param int $userId
     * @return View
     */
    public function edit(int $userId): View
    {
        $user = $this->userModel->getUser($userId);

        return view('users.edit', compact('user'));
    }

    /**
     * Update user
     *
     * @param UserUpdateRequest $request
     * @param $userId
     * @return RedirectResponse
     */
    public function update(UserUpdateRequest $request, $userId): RedirectResponse
    {
        $this->userService->update($userId, $request->all());

        return redirect()->route('user.index')->with('notification', 'User updated successfully.');
    }

    /**
     * Delete user
     *
     * @param UserDestroyRequest $request
     * @return RedirectResponse
     */
    public function destroy(UserDestroyRequest $request): RedirectResponse
    {
        $message = $this->userService->destroy($request->get('id'));

        return redirect()->route('user.index')->with('notification', $message);
    }
}
