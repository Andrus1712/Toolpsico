<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateactividadesRequest;
use App\Http\Requests\UpdateactividadesRequest;
use App\Repositories\actividadesRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\actividades;
use App\Models\acudiente;
use App\Models\comportamiento;
use App\Models\estudiante;
use App\Models\HistorialActividades;
use App\Models\Usuarios;
use App\Notifications\ActividadAsignada;
use App\Notifications\InvoicePaid;
use App\User;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Notifications\Messages\NexmoMessage;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash as FlashFlash;

class actividadesController extends AppBaseController
{
    /** @var  actividadesRepository */
    private $actividadesRepository;

    public function __construct(actividadesRepository $actividadesRepo)
    {
        $this->middleware('auth');
        $this->actividadesRepository = $actividadesRepo;
    }

    /**
     * Display a listing of the actividades.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $user = Auth()->user();
        if ($user->havePermission('show.actividades')) {
            $this->actividadesRepository->pushCriteria(new RequestCriteria($request));
            $actividades = $this->actividadesRepository->all();

            return view('actividades.index')
                ->with('actividades', $actividades);
        } else {
            return redirect('/home');
        }
    }

    public function getActividades()
    {
        $user = Auth()->user();

        $rol = $user->tieneRol();
        if ($rol == 'psi-user') {
            $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
                ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
                ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
                ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
                ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
                ->select(
                    'ac.id',
                    'ac.titulo',
                    'ac.fecha',
                    'ac.descripcion',
                    'ac.estado',
                    DB::raw('c.titulo as titulo_comportamiento'),
                    DB::raw('c.descripcion as descripcion_comportamiento'),
                    DB::raw('e.nombres as nombre_estudiante'),
                    DB::raw('e.apellidos as apellido_estudiante'),
                    DB::raw('e.telefono as telefono_estudiante'),
                    DB::raw('e.correo as correo_estudiante'),
                    DB::raw('a.nombres as nombre_acudiente'),
                    DB::raw('a.apellidos as apellido_acudiente'),
                    DB::raw('a.telefono as telefono_acudiente'),
                    DB::raw('a.correo as correo_acudiente'),
                    DB::raw('tc.titulo as titulo_tipo_comportamiento'),
                    DB::raw('tc.descripcion as descripcion_tipo_comportamiento'),
                    'ac.created_at',
                    'ac.deleted_at'
                )
                ->get();
        } else if ($rol == 'est-user') {
            $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
                ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
                ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
                ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
                ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
                ->select(
                    'ac.id',
                    'ac.titulo',
                    'ac.fecha',
                    'ac.descripcion',
                    'ac.estado',
                    DB::raw('c.titulo as titulo_comportamiento'),
                    DB::raw('c.descripcion as descripcion_comportamiento')
                )
                ->where(DB::raw('e.correo'), '=', Auth()->user()->email)
                ->get();
        } else if ($rol == 'doc-user') {
            $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
                ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
                ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
                ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
                ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
                ->select(
                    'ac.id',
                    'ac.titulo',
                    'ac.fecha',
                    'ac.descripcion',
                    'ac.estado',
                    DB::raw('c.titulo as titulo_comportamiento'),
                    DB::raw('c.descripcion as descripcion_comportamiento'),
                    DB::raw('e.nombres as nombre_estudiante'),
                    DB::raw('e.apellidos as apellido_estudiante'),
                    DB::raw('e.telefono as telefono_estudiante'),
                    DB::raw('e.correo as correo_estudiante'),
                    DB::raw('a.nombres as nombre_acudiente'),
                    DB::raw('a.apellidos as apellido_acudiente'),
                    DB::raw('a.telefono as telefono_acudiente'),
                    DB::raw('a.correo as correo_acudiente'),
                    DB::raw('tc.titulo as titulo_tipo_comportamiento'),
                    DB::raw('tc.descripcion as descripcion_tipo_comportamiento'),
                    'ac.created_at',
                    'ac.deleted_at'
                )
                ->get();
        } else if ($rol == 'acu-user') {
            $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
                ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
                ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
                ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
                ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
                ->select(
                    'ac.id',
                    'ac.titulo',
                    'ac.fecha',
                    'ac.descripcion',
                    'ac.estado',
                    DB::raw('c.titulo as titulo_comportamiento'),
                    DB::raw('c.descripcion as descripcion_comportamiento'),
                    DB::raw('e.nombres as nombre_estudiante'),
                    DB::raw('e.apellidos as apellido_estudiante'),
                    DB::raw('e.telefono as telefono_estudiante'),
                    DB::raw('e.correo as correo_estudiante'),
                    DB::raw('a.nombres as nombre_acudiente'),
                    DB::raw('a.apellidos as apellido_acudiente'),
                    DB::raw('a.telefono as telefono_acudiente'),
                    DB::raw('a.correo as correo_acudiente'),
                    DB::raw('tc.titulo as titulo_tipo_comportamiento'),
                    DB::raw('tc.descripcion as descripcion_tipo_comportamiento')
                )
                ->where(DB::raw('a.correo'), '=', Auth()->user()->email)
                ->get();
        } else {
            $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
                ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
                ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
                ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
                ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
                ->select(
                    'ac.id',
                    'ac.titulo',
                    'ac.fecha',
                    'ac.descripcion',
                    'ac.estado',
                    DB::raw('c.titulo as titulo_comportamiento'),
                    DB::raw('c.descripcion as descripcion_comportamiento'),
                    DB::raw('e.nombres as nombre_estudiante'),
                    DB::raw('e.apellidos as apellido_estudiante'),
                    DB::raw('e.telefono as telefono_estudiante'),
                    DB::raw('e.correo as correo_estudiante'),
                    DB::raw('a.nombres as nombre_acudiente'),
                    DB::raw('a.apellidos as apellido_acudiente'),
                    DB::raw('a.telefono as telefono_acudiente'),
                    DB::raw('a.correo as correo_acudiente'),
                    DB::raw('tc.titulo as titulo_tipo_comportamiento'),
                    DB::raw('tc.descripcion as descripcion_tipo_comportamiento'),
                    'ac.created_at',
                    'ac.deleted_at'
                )
                ->get();
        }

        //Permisos que tiene el usuario
        $permisos = [];

        if ($user->havePermission('edit.actividades')) {
            array_push($permisos, "edit.actividades");
        }

        if ($user->havePermission('delete.actividades')) {
            array_push($permisos, "delete.actividades");
        }

        if ($user->havePermission('create.actividades')) {
            array_push($permisos, "create.actividades");
        }

        $datos = [
            'actividades' => $actividades,
            'rol' => $rol,
            'permisos' => $permisos
        ];
        return response()->json($datos);
    }
    // public function getActividades()
    // {
    //     $queryUsers = DB::table('role_user')
    //         ->select('role_user.*')
    //         ->where('role_user.user_id', '=', Auth()->user()->id)
    //         ->limit(1)
    //         ->get();
    //     if (count($queryUsers) != 0) {
    //         if ($queryUsers[0]->role_id == 1) {

    //             $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
    //                 ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
    //                 ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
    //                 ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
    //                 ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
    //                 ->select(
    //                     'ac.id',
    //                     'ac.titulo',
    //                     'ac.fecha',
    //                     'ac.descripcion',
    //                     'ac.estado',
    //                     DB::raw('c.titulo as titulo_comportamiento'),
    //                     DB::raw('c.descripcion as descripcion_comportamiento'),
    //                     DB::raw('e.nombres as nombre_estudiante'),
    //                     DB::raw('e.apellidos as apellido_estudiante'),
    //                     DB::raw('e.telefono as telefono_estudiante'),
    //                     DB::raw('e.correo as correo_estudiante'),
    //                     DB::raw('a.nombres as nombre_acudiente'),
    //                     DB::raw('a.apellidos as apellido_acudiente'),
    //                     DB::raw('a.telefono as telefono_acudiente'),
    //                     DB::raw('a.correo as correo_acudiente'),
    //                     DB::raw('tc.titulo as titulo_tipo_comportamiento'),
    //                     DB::raw('tc.descripcion as descripcion_tipo_comportamiento'),
    //                     'ac.created_at',
    //                     'ac.deleted_at'
    //                 )
    //                 ->get();

    //             return response()->json($actividades);
    //         } else if ($queryUsers[0]->role_id == 2) {
    //             $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
    //                 ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
    //                 ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
    //                 ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
    //                 ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
    //                 ->select(
    //                     'ac.id',
    //                     'ac.titulo',
    //                     'ac.fecha',
    //                     'ac.descripcion',
    //                     'ac.estado',
    //                     DB::raw('c.titulo as titulo_comportamiento'),
    //                     DB::raw('c.descripcion as descripcion_comportamiento')
    //                 )
    //                 ->where(DB::raw('e.correo'), '=', Auth()->user()->email)
    //                 ->get();

    //             return response()->json($actividades);
    //         } else if ($queryUsers[0]->role_id == 3) {
    //             return response()->json("No permitido");
    //         } else if ($queryUsers[0]->role_id == 4) {
    //             $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
    //                 ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
    //                 ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
    //                 ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
    //                 ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
    //                 ->select(
    //                     'ac.id',
    //                     'ac.titulo',
    //                     'ac.fecha',
    //                     'ac.descripcion',
    //                     'ac.estado',
    //                     DB::raw('c.titulo as titulo_comportamiento'),
    //                     DB::raw('c.descripcion as descripcion_comportamiento'),
    //                     DB::raw('e.nombres as nombre_estudiante'),
    //                     DB::raw('e.apellidos as apellido_estudiante'),
    //                     DB::raw('e.telefono as telefono_estudiante'),
    //                     DB::raw('e.correo as correo_estudiante'),
    //                     DB::raw('a.nombres as nombre_acudiente'),
    //                     DB::raw('a.apellidos as apellido_acudiente'),
    //                     DB::raw('a.telefono as telefono_acudiente'),
    //                     DB::raw('a.correo as correo_acudiente'),
    //                     DB::raw('tc.titulo as titulo_tipo_comportamiento'),
    //                     DB::raw('tc.descripcion as descripcion_tipo_comportamiento')
    //                 )
    //                 ->where(DB::raw('a.correo'), '=', Auth()->user()->email)
    //                 ->get();

    //             return response()->json($actividades);
    //         }
    //     }
    // }

    public function getCountAct()
    {
        $queryUsers = DB::table('role_user')
            ->select('role_user.*')
            ->where('role_user.user_id', '=', Auth()->user()->id)
            ->limit(1)
            ->get();
        if (count($queryUsers) != 0) {
            if ($queryUsers[0]->role_id == 1) {
                $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
                    ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
                    ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
                    ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
                    ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
                    ->select(
                        'ac.id',
                        'ac.titulo',
                        'ac.fecha',
                        'ac.descripcion',
                        'ac.estado',
                    )
                    ->where(DB::raw('ac.estado'), '=', 0)
                    ->get();

                return response()->json($actividades);
            } else if ($queryUsers[0]->role_id == 2) {
                $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
                    ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
                    ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
                    ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
                    ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
                    ->select(
                        'ac.id',
                        'ac.titulo',
                        'ac.fecha',
                        'ac.descripcion',
                        'ac.estado',
                    )
                    ->where(DB::raw('e.correo'), '=', Auth()->user()->email)
                    ->where(DB::raw('ac.estado'), '=', 0)
                    ->get();

                return response()->json($actividades);
            } else if ($queryUsers[0]->role_id == 3) {
                return response()->json("No permitido");
            } else if ($queryUsers[0]->role_id == 4) {
                $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
                    ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
                    ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
                    ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
                    ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
                    ->select(
                        'ac.id',
                        'ac.titulo',
                        'ac.fecha',
                        'ac.descripcion',
                        'ac.estado',
                    )
                    ->where(DB::raw('a.correo'), '=', Auth()->user()->email)
                    ->where(DB::raw('ac.estado'), '=', 0)
                    ->get();

                return response()->json($actividades);
            }
        }
    }
    /**
     * Show the form for creating a new actividades.
     *
     * @return Response
     */
    public function create()
    {
        return view('actividades.create');
    }

    /**
     * Store a newly created actividades in storage.
     *
     * @param CreateactividadesRequest $request
     *
     * @return Response
     */
    public function store(Request $request)
    {

        $input = $request->all();

        $actividades = actividades::create([
            'titulo' => $request['titulo'],
            'descripcion' => $request['descripcion'],
            'fecha' => $request['fecha'],
            'estado' => $request['estado'],
            'comportamiento_id' => $request['comportamiento_id'],
            'tipo_comportamiento_id' => $request['tipo_comportamiento_id']
        ]);

        $estudiante_id = comportamiento::where('id', $request->comportamiento_id)->value('estudiante_id');
        $est = estudiante::find($estudiante_id);
        $user_id = User::where('email', $est->correo)->value('id');
        //Obtener el usuario estudiante
        $user_est = User::find($user_id);
        //Notificar a estudiante
        $user_est->notify(new ActividadAsignada($actividades));

        $acudiente = acudiente::find($est->acudiente_id);
        //Obtener al acudiente del estudiante
        $user_acud_id = User::where('email', $acudiente->correo)->value('id');
        $user_acud = User::find($user_acud_id);
        //Notificar a acudeinte
        $user_acud->notify(new ActividadAsignada($actividades));


        //Notificacion via sms
        // $nexmo = app('Nexmo\Client');

        // $nexmo->message()->send([
        //     'to'   => '57'.$est->telefono,
        //     'from' => '573177765722',
        //     'text' => 'Hola '. $est->nombres . ', Tienes una nueva actividad: ' . $request->titulo .' fecha:'. $request->fecha
        // ]);

        return redirect(route('actividades.index'));
    }

    /**
     * Display the specified actividades.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $queryUsers = DB::table('role_user')
            ->select('role_user.*')
            ->where('role_user.user_id', '=', Auth()->user()->id)
            ->limit(1)
            ->get();
        if (count($queryUsers) != 0) {
            if ($queryUsers[0]->role_id == 1) {
                $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
                    ->where(DB::raw('ac.id'), '=', $id)
                    ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
                    ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
                    ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
                    ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
                    ->select(
                        'ac.id',
                        'ac.titulo',
                        'ac.fecha',
                        'ac.descripcion',
                        'ac.estado',
                        DB::raw('c.titulo as titulo_comportamiento'),
                        DB::raw('c.descripcion as descripcion_comportamiento'),
                        DB::raw('e.nombres as nombre_estudiante'),
                        DB::raw('e.apellidos as apellido_estudiante'),
                        DB::raw('e.telefono as telefono_estudiante'),
                        DB::raw('e.correo as correo_estudiante'),
                        DB::raw('a.nombres as nombre_acudiente'),
                        DB::raw('a.apellidos as apellido_acudiente'),
                        DB::raw('a.telefono as telefono_acudiente'),
                        DB::raw('a.correo as correo_acudiente'),
                        DB::raw('tc.titulo as titulo_tipo_comportamiento'),
                        DB::raw('tc.descripcion as descripcion_tipo_comportamiento'),
                        'ac.created_at',
                        'ac.deleted_at'
                    )
                    ->get();

                if (count($actividades) > 0) {

                    return view('actividades.show')->with('actividades', $actividades);
                } else {

                    Flash::error('Actividades not found');

                    return redirect(route('actividades.index'));
                }
                // dd($actividades);
            } else if ($queryUsers[0]->role_id == 2) {
                $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
                    ->where(DB::raw('ac.id'), '=', $id)
                    ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
                    ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
                    ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
                    ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
                    ->select(
                        'ac.id',
                        'ac.titulo',
                        'ac.fecha',
                        'ac.descripcion',
                        'ac.estado',
                        DB::raw('c.titulo as titulo_comportamiento'),
                        DB::raw('c.descripcion as descripcion_comportamiento')
                    )
                    ->where(DB::raw('e.correo'), '=', Auth()->user()->email)
                    ->get();

                if (count($actividades) > 0) {

                    return view('actividades.show')->with('actividades', $actividades);
                } else {

                    Flash::error('Actividades not found');

                    return redirect(route('actividades.index'));
                }
            } else if ($queryUsers[0]->role_id == 3) {
                Flash::error('Actividades not found');
                return redirect(route('actividades.index'));
            } else if ($queryUsers[0]->role_id == 4) {
                $actividades = DB::table(DB::raw('actividades ac'))->where(DB::raw('ac.deleted_at', '=', NULL))
                    ->where(DB::raw('ac.id'), '=', $id)
                    ->join(DB::raw('comportamientos c'), 'ac.comportamiento_id', '=', 'c.id')
                    ->join(DB::raw('estudiantes e'), 'c.estudiante_id', '=', 'e.id')
                    ->join(DB::raw('acudientes a'), 'e.acudiente_id', '=', 'a.id')
                    ->join(DB::raw('tipo_comportamientos tc'), 'ac.tipo_comportamiento_id', '=', 'tc.id')
                    ->select(
                        'ac.id',
                        'ac.titulo',
                        'ac.fecha',
                        'ac.descripcion',
                        'ac.estado',
                        DB::raw('c.titulo as titulo_comportamiento'),
                        DB::raw('c.descripcion as descripcion_comportamiento'),
                        DB::raw('e.nombres as nombre_estudiante'),
                        DB::raw('e.apellidos as apellido_estudiante'),
                        DB::raw('e.telefono as telefono_estudiante'),
                        DB::raw('e.correo as correo_estudiante'),
                        DB::raw('a.nombres as nombre_acudiente'),
                        DB::raw('a.apellidos as apellido_acudiente'),
                        DB::raw('a.telefono as telefono_acudiente'),
                        DB::raw('a.correo as correo_acudiente'),
                        DB::raw('tc.titulo as titulo_tipo_comportamiento'),
                        DB::raw('tc.descripcion as descripcion_tipo_comportamiento')
                    )
                    ->where(DB::raw('a.correo'), '=', Auth()->user()->email)
                    ->get();

                if (count($actividades) > 0) {

                    return view('actividades.show')->with('actividades', $actividades);
                } else {

                    Flash::error('Actividades not found');

                    return redirect(route('actividades.index'));
                }
            }
        }
    }

    /**
     * Show the form for editing the specified actividades.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $actividades = $this->actividadesRepository->findWithoutFail($id);

        if (empty($actividades)) {
            Flash::error('Actividades not found');

            return redirect(route('actividades.index'));
        }

        return view('actividades.edit')->with('actividades', $actividades);
    }

    /**
     * Update the specified actividades in storage.
     *
     * @param  int              $id
     * @param UpdateactividadesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateactividadesRequest $request)
    {
        $actividades = $this->actividadesRepository->findWithoutFail($id);

        if (empty($actividades)) {
            Flash::error('Actividades not found');

            return redirect(route('actividades.index'));
        }

        $actividades = $this->actividadesRepository->update($request->all(), $id);

        Flash::success('Actividades updated successfully.');

        return redirect(route('actividades.index'));
    }

    /**
     * Remove the specified actividades from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $actividades = $this->actividadesRepository->findWithoutFail($id);

        if (empty($actividades)) {
            Flash::error('Actividades not found');

            return redirect(route('actividades.index'));
        }

        $this->actividadesRepository->delete($id);

        Flash::success('Actividades deleted successfully.');

        return redirect(route('actividades.index'));
    }

    public function getHistorial($id)
    {

        $historial = DB::table(DB::raw('historial_actividades ha'))
            ->where(DB::raw('ha.deleted_at', '=', null))
            ->join(DB::raw('actividades a'), 'ha.actividad_id', '=', 'a.id')
            ->select(DB::raw('a.id'), DB::raw('a.titulo'), DB::raw('a.fecha'), DB::raw('ha.fecha_historial'), DB::raw('ha.estado_actividad'))
            ->where(DB::raw('a.id'), '=', $id)
            ->get();

        return response()->json($historial);
    }
}
