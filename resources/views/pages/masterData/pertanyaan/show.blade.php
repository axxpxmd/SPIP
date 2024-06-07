@extends('layouts.app')
@section('title', '| Data Pertanyaan')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h4>
                        <i class="icon icon-question"> </i>
                        Menampilkan {{ $title }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route($route.'index') }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-question-circle-o"></i>Pertanyaan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab2" data-toggle="tab" href="#tambah-data" role="tab"><i class="icon icon-edit"></i>Edit Data</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content my-3" id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <h6 class="card-header"><strong>Data Pertanyaan</strong></h6>
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Indikator :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pertanyaan->indikator->n_indikator }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Status :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pertanyaan->status_wajib == 1 ? 'Wajib' : 'Tidak Wajib' }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Pertanyaan :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pertanyaan->n_question }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane animated fadeInUpShort show" id="tambah-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div id="alert"></div>
                        <div class="card">
                            <h6 class="card-header"><strong>Edit Data</strong></h6>
                            <div class="card-body">
                                <form class="needs-validation" id="form" method="PATCH"  enctype="multipart/form-data" novalidate>
                                    {{ method_field('PATCH') }}
                                    <input type="hidden" id="id" name="id" value="{{ $pertanyaan->id }}"/>
                                    <div class="form-row form-inline">
                                        <div class="col-md-8">
                                            <div class="form-group m-0">
                                                <label class="col-form-label s-12 col-md-2">Indikator<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-8 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" id="indikator_id" name="indikator_id" autocomplete="off">
                                                        <option value="">Pilih</option>
                                                        @foreach ($indikators as $i)
                                                            <option value="{{ $i->id }}">{{ $i->n_indikator }} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mt-1">
                                                <label class="col-form-label s-12 col-md-2">Status<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-8 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" id="status_wajib" name="status_wajib" autocomplete="off">
                                                        <option value="">Pilih</option>
                                                        <option value="1" {{ $pertanyaan->status_wajib == 1 ? 'selected' : '-'  }}>Wajib</option>
                                                        <option value="0" {{ $pertanyaan->status_wajib == 0 ? 'selected' : '-' }}>Tidak Wajib</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mt-1">
                                                <label for="n_question" class="col-form-label s-12 col-md-2">Pertanyaan<span class="text-danger ml-1">*</span></label>
                                                <textarea name="n_question" cols="10" rows="5" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required>{{ $pertanyaan->n_question }}</textarea>
                                            </div>
                                            <div class="form-group mt-2">
                                                <div class="col-md-2"></div>
                                                <button type="submit" class="btn btn-primary btn-sm" id="action"><i class="icon-save mr-2"></i>Simpan Perubahan<span id="txtAction"></span></button>
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
    $('#indikator_id').val("{{$pertanyaan->indikator_id}}");
    $('#indikator_id').trigger('change.select2');

    $('#form').on('submit', function (e) {
        if ($(this)[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        else{
            $('#alert').html('');
            $('#action').attr('disabled', true);
            url = "{{ route($route.'update', ':id') }}".replace(':id', $('#id').val());
            $.ajax({
                url : url,
                type : 'POST',
                data: new FormData(($(this)[0])),
                contentType: false,
                processData: false,
                success : function(data) {
                    console.log(data);
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
                                keys: ['enter'],
                                action: function () {
                                    location.reload();
                                }
                            }
                        }
                    });
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
                    $('#action').removeAttr('disabled');
                }
            });
            return false;
        }
        $(this).addClass('was-validated');
    });
</script>
@endsection
