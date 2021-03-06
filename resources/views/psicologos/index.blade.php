@extends('layouts.app')

@section('content')
<section class="content-header">
    <h1 class="pull-left">Psicoorientadores</h1>

    @if (Auth()->user()->havePermission('create.psicologos'))
    <h1 class="pull-right">
        <a class="btn btn-success pull-right" style="margin-top: -10px;margin-bottom: 5px" id="add-psicologos"><i
                class="fa fa-plus"></i> Agregar</a>
    </h1>
    @endif
    @include('psicologos.create')
</section>
<div class="content">
    <div class="clearfix"></div>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">
            @include('psicologos.table')
        </div>
    </div>
    <div class="text-center">

    </div>
</div>
@include('layouts.scripts')
<script src="js/psicologos/main.js"></script>
@endsection