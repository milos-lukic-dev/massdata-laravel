<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\AssignUserPermissionRequest;
use App\Http\Requests\Permission\PermissionDestroyRequest;
use App\Http\Requests\Permission\PermissionStoreRequest;
use App\Http\Requests\Permission\RemoveUserPermissionRequest;
use App\Http\Requests\Permission\PermissionUpdateRequest;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller responsible for displaying permission actions
 *
 * @class PermissionController
 */
class PermissionController extends Controller
{
    protected PermissionService $permissionService;
    protected User $userModel;

    /**
     * Constructor.
     *
     * @param PermissionService $permissionService
     * @param User $user
     */
    public function __construct(PermissionService $permissionService, User $user)
    {
        $this->permissionService = $permissionService;
        $this->userModel = $user;
    }

    /**
     * Index page
     * @return View
     */
    public function index(): View
    {
        $permissions = $this->permissionService->getPermissions();

        return view('permissions.index', compact('permissions'));
    }

    /**
     * Create user form
     *
     * @return View
     */
    public function create(): View
    {
        return view('permissions.create');
    }

    /**
     * Store user
     *
     * @param PermissionStoreRequest $request
     * @return RedirectResponse
     */
    public function store(PermissionStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $message = $this->permissionService->create($data['name']);

        return redirect()->route('permission.index')->with('notification', $message);
    }

    /**
     * Edit permission
     *
     * @param int $permissionId
     * @return \Illuminate\Container\Container|mixed|object
     */
    public function edit(int $permissionId)
    {
        $permission = $this->permissionService->petPermission($permissionId);

        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update permission
     *
     * @param PermissionUpdateRequest $request
     * @return mixed
     */
    public function update(PermissionUpdateRequest $request)
    {
        $message = $this->permissionService->update($request->all());

        return redirect()->route('permission.index')->with('notification', $message);
    }

    /**
     * Destroy permission
     *
     * @param PermissionDestroyRequest $request
     * @return void
     */
    public function destroy(PermissionDestroyRequest $request)
    {
        $message = $this->permissionService->destroy($request->get('id'));

        return redirect()->route('permission.index')->with('notification', $message);
    }

    /**
     * Users
     *
     * @param $permissionId
     * @return View
     */
    public function users($permissionId): View
    {
        $permissions = $this->permissionService->getUsersByPermissionId($permissionId);

        return view('permissions.users', compact('permissions'));
    }

    /**
     * Search user by email
     *
     * @param Request $request
     * @return mixed
     */
    public function searchUser(Request $request)
    {
        $term = $request->get('q');

        return $this->userModel->getUsersByEmail($term);
    }

    /**
     * Assign user permission
     *
     * @param AssignUserPermissionRequest $request
     * @return mixed
     */
    public function assignUserPermission(AssignUserPermissionRequest $request)
    {
        $data = $request->all();

        $message = $this->permissionService->assignUserPermission($data);

        return redirect()->route('permission.users', $data['permission_id'])->with('notification', $message);
    }

    /**
     * Remove user permission
     *
     * @param RemoveUserPermissionRequest $request
     * @return mixed
     */
    public function removeUserPermission(RemoveUserPermissionRequest $request)
    {
        $data = $request->all();

        $message = $this->permissionService->removeUserPermission($data);

        return redirect()->route('permission.users', $data['permission_id'])->with('notification', $message);
    }
}
