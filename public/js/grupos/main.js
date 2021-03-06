var modal = $("#modal-grupos")
var AllRegister = []

var permisos = []


$(document).ready(function() {
    Reload()

    $("#grupos-table").on('click', '[id^=Btn_Edit_]', function() {

        var id = $(this).attr('data-id')
        var filtro = AllRegister.filter(f => f.id == id);

        if (filtro.length != 0) {
            modal.modal('show')
            Modal()
            LoadDocente()

            $("#save").text("Actualizar")
            $("#save").attr('id', 'update')


            $("#grado").val(filtro[0].grado)
            $("#curso").val(filtro[0].curso)
            $("#docente_id").val(filtro[0].docente_id)

            $('#update').on('click', function() {
                var grado = $("#grado").val(),
                    curso = $("#curso").val(),
                    docente_id = $("#docente_id").val();

                if (grado == '' || curso == '' || docente_id == '') {
                    toastr.warning("Complete todos los campos")
                } else {
                    $('#loading-spinner').show();
                    $.ajax({
                            url: '/api/grupos/' + id,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'PUT',
                            data: {
                                grado: grado,
                                curso: curso,
                                docente_id: docente_id
                            },
                        })
                        .done(function(response) {
                            var success = response.success;
                            var messages = response.message;
                            if (success) {
                                setTimeout(function() {
                                    $('#loading-spinner').hide();
                                    modal.modal("hide")
                                }, 600);
                                toastr.info("Infomacion actualizada");
                                Reload();
                            } else {
                                // Mensaje de validacion
                                $('#loading-spinner').hide();
                                Object.keys(messages).forEach(type => {
                                    console.log(messages[type]);
                                    toastr.error(messages[type]);
                                });
                            }
                        })
                        .fail(function() {
                            $('#loading-spinner').hide();
                            toastr.error("Ha ocurrido un error");
                        })
                        .always(function() {
                            $("#update").addClass("disabled");
                        });
                }
            })


        }
    })

    $('#grupos-table').on('click', '[id^=Btn_delete_]', function() {
        var id = $(this).attr('data-id')

        swal({
                title: "¿Realmente deseas eliminar el grado y el curso?",
                text: "Ten en cuenta que eliminaras toda su información del sistema",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Si, eliminar",
                closeOnConfirm: false
            },
            function() {
                $.ajax({
                        url: "/api/grupos/" + id,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    })
                    .done(function() {
                        swal("Eliminado!", "Se ha eliminado el acudiente", "success");
                        Reload();
                    })
                    .fail(function() {
                        swal("Error!", "Ha ocurrido un error", "error");
                    });

            });


    })

    $('#add-grupo').on('click', function() {
        modal.modal('show')
        Modal()
        LoadDocente()

        $('#save').on('click', function() {
            var grado = $("#grado").val(),
                curso = $("#curso").val(),
                docente_id = $("#docente_id").val();


            if (grado == '' || curso == '' || docente_id == null) {
                toastr.warning("Complete todos los campos")
            } else {
                $('#loading-spinner').show();
                $.ajax({
                        url: '/api/grupos',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: {
                            grado: grado,
                            curso: curso,
                            docente_id: docente_id
                        },
                    })
                    .done(function(response) {
                        var success = response.success;
                        var messages = response.message;
                        if (success) {
                            setTimeout(function() {
                                $('#loading-spinner').hide();
                                modal.modal("hide")
                            }, 600);
                            toastr.success("Grupo y grado creado correctamente");
                            Reload();
                        } else {
                            // Mensaje de validacion
                            $('#loading-spinner').hide();
                            Object.keys(messages).forEach(type => {
                                console.log(messages[type]);
                                toastr.error(messages[type]);
                            });
                        }
                    })
                    .fail(function() {
                        $('#loading-spinner').hide();
                        toastr.error("Ha ocurrido un error");
                    })
                    .always(function() {
                        $("#save").addClass("disabled");
                    });
            }
        })

    })

});

function Modal() {
    modal.find('.modal-content').empty().append( /* html */ `
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Formulario de Grado y grupos</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Grado: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-graduation-cap"></i></span>
                        <input id="grado" placeholder="Digite grado" class="form-control" type="text" maxlength="2">
                    </div>
                </div>

                <div class="form-group">
                    <label >Docente encargado: </label>
                    <select class="form-control" id="docente_id" style="width: 100%;">

                    </select>
                </div>

            </div>

            <div class="col-md-6">

                <div class="form-group">
                    <label>Grupo: </label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-graduation-cap"></i></span>
                        <input id="curso" placeholder="Digite curso" class="form-control" type="text" maxlength="1">
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="save">Guardar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
    </div>
    `)
}

function LoadDocente() {
    $("#docente_id").select2({
        placeholder: 'Seleccione el director de grupo',
        allowClear: true,
        dropdownParent: modal,
        width: 'resolve'
    });

    $.ajax({
            url: '/api/docentes',
        })
        .done(function(response) {
            for (var i in response.data) {
                $("#docente_id").append(`<option value='${response.data[i].id}'>${response.data[i].nombres} ${response.data[i].apellidos}</option>`)
            }

        })
        .fail(function() {
            console.log("error");
        })
}

function Reload() {
    $.ajax({
        url: "/getGrupos",
        type: "GET",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "JSON",
    })

    .done(function(response) {
        if (response.length != 0) {
            AllRegister = response.cursos;

            permisos = response.permisos;

            DataTable(response.cursos);
        } else {
            $('#grupos-table').dataTable().fnClearTable();
            $('#grupos-table').dataTable().fnDestroy();
            $('#grupos-table thead').empty()
        }
    })

    .fail(function() {
        console.log("error");
    });
}

function DataTable(response) {

    console.log(response)
    if ($.fn.DataTable.isDataTable('#grupos-table')) {
        $('#grupos-table').dataTable().fnClearTable();
        $('#grupos-table').dataTable().fnDestroy();
        $('#grupos-table thead').empty()
    } else {
        $('#grupos-table thead').empty()
    }


    if (response.length != 0) {
        let my_columns = []
        $.each(response[0], function(key, value) {
            var my_item = {};
            // my_item.class = "filter_C";
            my_item.data = key;
            if (key == 'created_at') {

                my_item.title = 'Acción';

                my_item.render = function(data, type, row) {
                    var html = '';
                    for (let i = 0; i < permisos.length; i++) {
                        if (permisos[i] == "delete.cursos") {
                            html += `
                                    <a data-id=${row.id} id="Btn_delete_${row.id}" class='btn btn-circle btn-sm btn-danger'>
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </a> `
                        } else if (permisos[i] == "edit.cursos") {
                            html += `
                                    <a data-id=${row.id} id="Btn_Edit_${row.id}" class='btn btn-circle btn-sm btn-primary'>
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                    </a>`
                        }
                    }
                    return `<div align="center">
                                <div class="btn-group btn-group-circle btn-group-solid" align="center">
                                    ${html}
                                </div>
                                
                            </div>`

                }
                if (permisos.length != 0) {
                    my_columns.push(my_item);
                }

                // } else if (key == 'id') {

                //     my_item.title = '#';

                //     my_item.render = function(data, type, row) {
                //         return `  <div'> 
                //                     ${row.id}
                //                 </div>`
                //     }
                //     my_columns.push(my_item);
            } else if (key == 'grado') {
                my_item.title = 'Grado';

                my_item.render = function(data, type, row) {
                    return `  <div'> 
                                ${row.grado}
                            </div>`
                }
                my_columns.push(my_item);

            } else if (key == 'curso') {

                my_item.title = 'Grupo';

                my_item.render = function(data, type, row) {
                    return `  <div'> 
                                ${row.curso}
                            </div>`
                }
                my_columns.push(my_item);
            } else if (key == 'docente') {

                my_item.title = 'Director de curso';

                my_item.render = function(data, type, row) {
                    return `  <div'> 
                                ${row.docente}
                            </div>`
                }
                my_columns.push(my_item);
            }
        })

        $('#grupos-table').DataTable({
            "scrollX": my_columns.length >= 5 ? true : false,
            "destroy": false,
            responsive: true,
            "destroy": true,
            bProcessing: true,
            bAutoWidth: false,
            data: response,
            "columns": my_columns,
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No hay datos registrados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Grados y gupos",
                "infoEmpty": "No hay grupos registrados",
                "infoFiltered": "(Filtrado de _MAX_  Grados y gupos)",
                "lengthMenu": "_MENU_ Grado y grupos",
                "search": "Buscar:",
                "zeroRecords": "No se han encontrado registros"
            },

            "order": [
                [0, 'asc']
            ]
        });
    }
}