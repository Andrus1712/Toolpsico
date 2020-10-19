<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUsuariosRequest;
use App\Http\Requests\UpdateUsuariosRequest;
use App\Repositories\UsuariosRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;

class UsuariosController extends AppBaseController
{
    /** @var  UsuariosRepository */
    private $usuariosRepository;

    public function __construct(UsuariosRepository $usuariosRepo)
    {
        $this->usuariosRepository = $usuariosRepo;
    }

    /**
     * Display a listing of the Usuarios.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {

        $this->usuariosRepository->pushCriteria(new RequestCriteria($request));
        $usuarios = User::all();

        return view('usuarios.index')->with('usuarios', $usuarios);
    }

    /**
     * Show the form for creating a new Usuarios.
     *
     * @return Response
     */
    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Store a newly created Usuarios in storage.
     *
     * @param CreateUsuariosRequest $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // $input = $request->all();
        // $usuarios = $this->usuariosRepository->create($input);

        $role_id = $request->role_id;

        $role = Role::find($role_id);

        $usuarios = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        $usuarios->asignarRol($role);


        Flash::success('Usuarios saved successfully.');

        return redirect(route('usuarios.index'));
    }

    public function readNotification(Request $request)
    {
        $notify_id = $request->id;

        $notification = auth()->user()->unreadNotifications->find($notify_id);
        if ($notification) {
            $notification->markAsRead();
        }
        return redirect()->back();
    }

    /**
     * Display the specified Usuarios.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $usuarios = $this->usuariosRepository->findWithoutFail($id);

        if (empty($usuarios)) {
            Flash::error('Usuarios not found');

            return redirect(route('usuarios.index'));
        }

        return view('usuarios.show')->with('usuarios', $usuarios);
    }

    /**
     * Show the form for editing the specified Usuarios.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $usuarios = $this->usuariosRepository->findWithoutFail($id);

        if (empty($usuarios)) {
            Flash::error('Usuarios not found');

            return redirect(route('usuarios.index'));
        }

        return view('usuarios.edit')->with('usuarios', $usuarios);
    }

    /**
     * Update the specified Usuarios in storage.
     *
     * @param  int              $id
     * @param UpdateUsuariosRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUsuariosRequest $request)
    {
        $usuarios = $this->usuariosRepository->findWithoutFail($id);

        if (empty($usuarios)) {
            Flash::error('Usuarios not found');

            return redirect(route('usuarios.index'));
        }

        $usuarios = $this->usuariosRepository->update($request->all(), $id);

        Flash::success('Usuarios updated successfully.');

        return redirect(route('usuarios.index'));
    }

    /**
     * Remove the specified Usuarios from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $usuarios = $this->usuariosRepository->findWithoutFail($id);

        if (empty($usuarios)) {
            Flash::error('Usuarios not found');

            return redirect(route('usuarios.index'));
        }

        $this->usuariosRepository->delete($id);

        Flash::success('Usuarios deleted successfully.');

        return redirect(route('usuarios.index'));
    }
}