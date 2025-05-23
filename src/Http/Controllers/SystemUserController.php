<?php

namespace Module\System\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Module\System\Models\SystemUser;
use Module\System\Http\Resources\RoleCollection;
use Module\System\Http\Resources\RoleShowResource;
use Module\System\Jobs\SystemGrantPermission;

class SystemUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view', SystemUser::class);

        return new RoleCollection(
            SystemUser::applyMode($request->trashed)
                ->filter($request->filters)
                ->search($request->findBy)
                ->sortBy($request->sortBy)
                ->paginate($request->itemsPerPage)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('create', SystemUser::class);

        $request->validate([]);

        return SystemUser::storeRecord($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Module\System\Models\SystemUser $systemUser
     * @return \Illuminate\Http\Response
     */
    public function show(SystemUser $systemUser)
    {
        Gate::authorize('show', $systemUser);

        return new RoleShowResource($systemUser);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Module\System\Models\SystemUser $systemUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SystemUser $systemUser)
    {
        Gate::authorize('update', $systemUser);

        $request->validate([]);

        return SystemUser::updateRecord($request, $systemUser);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Module\System\Models\SystemUser $systemUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(SystemUser $systemUser)
    {
        Gate::authorize('delete', $systemUser);

        return SystemUser::deleteRecord($systemUser);
    }

    /**
     * Restore the specified resource from soft-delete.
     *
     * @param  \Module\System\Models\SystemUser $systemUser
     * @return \Illuminate\Http\Response
     */
    public function restore(SystemUser $systemUser)
    {
        Gate::authorize('restore', $systemUser);

        return SystemUser::restoreRecord($systemUser);
    }

    /**
     * Force Delete the specified resource from soft-delete.
     *
     * @param  \Module\System\Models\SystemUser $systemUser
     * @return \Illuminate\Http\Response
     */
    public function forceDelete(SystemUser $systemUser)
    {
        Gate::authorize('destroy', $systemUser);

        return SystemUser::destroyRecord($systemUser);
    }

    /**
     * search function
     *
     * @param Request $request
     * @return void
     */
    public function search(Request $request)
    {
        return SystemUser::where('name', 'LIKE', '%' . $request->search . '%')
            ->limit(10)
            ->forCombo();
    }

    /**
     * grantPermissions function
     *
     * @param Request $request
     * @return void
     */
    public function grantPermissions(Request $request)
    {
        try {
            SystemUser::select('id', 'name')->chunk(100, function ($users) {
                foreach ($users as $user) {
                    SystemGrantPermission::dispatch($user->id);
                }
            });
    
            return response()->json([
                'status'    => true,
                'message'   => 'Grant all user permission has been completed.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage()
            ], $e->getCode());
        }
    }
}
