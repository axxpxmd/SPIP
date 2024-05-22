@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-document-text mr-1"></i>
                        {{ $title }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-home2"></i>Semua Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab2" data-toggle="tab" href="#tambah-data" role="tab"><i class="icon icon-plus"></i>Tambah Data</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid my-3 relative animatedParent animateOnce">
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card no-b">
                            <div class="card-body">
                                <div class="form-group row mb-1">
                                    <label for="tahunId" class="col-form-label s-12 col-md-2 text-right font-weight-bold">Tahun :</label>
                                    <div class="col-sm-2">
                                        <select name="tahunId" id="tahunId" class="select2 form-control r-0 light s-12" onchange="selectOnChange()">
                                            <option value="0">Semua</option>
                                            @foreach ($tahuns as $i)
                                            <option value="{{ $i->id }}">{{ $i->tahun }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="indikator_id_filter" class="col-form-label s-12 col-md-2 text-right font-weight-bold">Indikator :</label>
                                    <div class="col-sm-8">
                                        <select name="indikator_id_filter" id="indikator_id_filter" class="select2 form-control r-0 light s-12" onchange="selectOnChange()">
                                            <option value="">Semua</option>
                                            @foreach ($indikators as $i)
                                                <option value="{{ $i->id }}">{{ $i->n_indikator }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="card no-b">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <th width="5%"></th>
                                            <th width="90%">Nama Quesioner</th>
                                            <th width="5%"></th>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane animated fadeInUpShort" id="tambah-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div id="alert"></div>
                        <div class="card">
                            <h6 class="card-header"><strong>Tambah Data</strong></h6>
                            <div class="card-body">
                                <form class="needs-validation" id="form" method="GET"  enctype="multipart/form-data" novalidate>
                                    {{ method_field('GET') }}
                                    <div class="form-row form-inline">
                                        <div class="col-md-12">
                                            <div class="form-group m-0">
                                                <label class="col-form-label s-12 col-md-2">Tahun<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-2 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" id="tahun_id" name="tahun_id" autocomplete="off">
                                                        <option value="">Pilih</option>
                                                        @foreach ($tahuns as $i)
                                                            <option value="{{ $i->id }}">{{ $i->tahun }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mt-1">
                                                <label class="col-form-label s-12 col-md-2">Indikator<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-9 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" id="indikator_id" name="indikator_id" autocomplete="off">
                                                        <option value="">Pilih</option>
                                                        @foreach ($indikators as $i)
                                                            <option value="{{ $i->id }}">{{ $i->n_indikator }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mt-1">
                                                <label class="col-form-label s-12 col-md-2">Pertanyaan<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-9 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" name="question_id" id="pertanyaan_id" autocomplete="off">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mt-1">
                                                <label for="total_jawaban" class="col-form-label s-12 col-md-2">Total Jawaban<span class="text-danger ml-1">*</span></label>
                                                <input type="number" name="total_jawaban" id="total_jawaban" value="6" class="form-control r-0 light s-12 col-md-2" autocomplete="off" required/>
                                            </div>
                                            <div class="form-group mt-2">
                                                <div class="col-md-2"></div>
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="icon-arrow_forward mr-2"></i>Selanjutnya</button>
                                                <a class="btn btn-sm" onclick="add()" id="reset">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

    function format ( d ) {
        return  '<div class="mt-2">'+
                    '<label class="col-md-1 text-right s-12">'+
                        '<strong>Tahun :</strong>'+
                    '</label>'+
                    '<label class="col-md-11 s-12">'
                        +d.tahun_id+
                    '</label>'+
                    '<label class="col-md-1 text-right s-12">'+
                        '<strong>Indikator :</strong>'+
                    '</label>'+
                    '<label class="col-md-11 s-12">'
                        +d.indikator_id+
                    '</label>'+
                '</div>'
    }

    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        order: [ 0, 'asc' ],
        ajax: {
            url: "{{ route($route.'api') }}",
            method: 'POST',
            data: function (data) {
                data.indikator_id = $('#indikator_id_filter').val();
                data.tahun_id = $('#tahunId').val();
            }
        },
        columns: [
            {"className": 'details-control', "orderable": false, "data": null, "defaultContent": ''},
            // {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, align: 'center', className: 'text-center'},
            {data: 'question_id', name: 'question_id'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
        ],
    });

    // Add event listener for opening and closing details
    $('#dataTable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );

    $('#indikator_id').on('change', function(){
        val = $(this).val();
        option = "<option value=''>&nbsp;</option>";
        if(val == ""){
            $('#pertanyaan_id').html(option);
        }else{
            $('#pertanyaan_id').html("<option value=''>Loading...</option>");
            url = "{{ route('kuesioner.getPertanyaan', ':id') }}".replace(':id', val);
            $.get(url, function(data){
                if(data){
                    $.each(data, function(index, value){
                        option += "<option value='" + value.id + "'>" + value.n_question +"</li>";
                    });
                    $('#pertanyaan_id').empty().html(option);

                    $("#pertanyaan_id").val($("#pertanyaan_id option:first").val()).trigger("change.select2");
                }else{
                    $('#pertanyaan_id').html(option);
                }
            }, 'JSON');
        }
    });

    function selectOnChange(){
        $('#dataTable').DataTable().ajax.reload();
    }

    function add(){
        save_method = "add";
        $('#form').trigger('reset');
        $('#reset').show();
        $('#pertanyaan_id').val("");
        $('#pertanyaan_id').trigger('change.select2');
        $('#indikator_id').val("");
        $('#indikator_id').trigger('change.select2');
        $('#tahun_id').val("");
        $('#tahun_id').trigger('change.select2');
        $('#total_jawaban').val("");
    }

    $('#form').on('submit', function (e) {
        if ($(this)[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        else{
            var tahunId = $('#tahun_id').val();
            var indikatorId = $('#indikator_id').val();
            var questionId = $('#pertanyaan_id').val();
            var totalJawaban = $('#total_jawaban').val();
            $.ajax({
                type: 'GET',
                url : "{{ route($route.'check') }}",
                data: {
                    tahun_id: tahunId,
                    indikator_id : indikatorId,
                    question_id: questionId,
                    total_jawaban : totalJawaban
                },
                success: function (data) {
                    window.location = "{{ route($route.'create') }}?tahun_id=" + data.tahun_id + "&indikator_id=" + data.indikator_id + "&question_id=" + data.question_id + "&total_jawaban=" + data.total_jawaban;
                },
                error : function(data){
                    err = '';
                    respon = data.responseJSON;
                    if(respon.errors){
                        $.each(respon.errors, function( index, value ) {
                            err = err + "<li>" + value +"</li>";
                        });
                    }
                    $('#alert').html("<div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>Error!</strong> " + respon.message + "<ol class='pl-3 m-0'>" + err + "</ol></div>");
                }
            }); return false;
        }
        $(this).addClass('was-validated');
    });

    function remove(id){
        $.confirm({
            title: '',
            content: 'Apakah Anda yakin akan menghapus data ini ?',
            icon: 'icon icon-question amber-text',
            theme: 'modern',
            closeIcon: true,
            animation: 'scale',
            type: 'red',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn-primary',
                    keys: ['enter'],
                    action: function(){
                        $.post("{{ route($route.'destroy', ':id') }}".replace(':id', id), {'_method' : 'DELETE'}, function(data) {
                            $('#dataTable').DataTable().ajax.reload();
                            $.confirm({
                                title: 'Success',
                                content: data.message,
                                icon: 'icon icon-check',
                                theme: 'modern',
                                closeIcon: true,
                                animation: 'scale',
                                autoClose: 'ok|3000',
                                type: 'green',
                                buttons: {
                                    ok: {
                                        text: "ok!",
                                        btnClass: 'btn-primary',
                                        keys: ['enter']
                                    }
                                }
                            });
                        }, "JSON").fail(function(){
                            reload();
                        });
                    }
                },
                cancel: function(){}
            }
        });
    }
</script>
@endsection
