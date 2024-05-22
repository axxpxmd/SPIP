@extends('layouts.app')
@section('title', '| Data Quesioner')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h4>
                        <i class="icon icon-document-text"> </i>
                        Menampilkan {{ $title }} {{ $quesioner->tahun->tahun }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route($route.'index') }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-document-text"></i>Quesioner</a>
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
                        @if ($checkDuplikat != $totalJawaban)
                        <div class="alert alert-warning mb-0" role="alert">
                            Terdapat duplikasi jawaban.
                        </div>
                        @endif
                        <div class="card mt-2">
                            <h6 class="card-header"><strong>Data Quesioner</strong></h6>
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Tahun :</strong></label>
                                        <label class="col-md-10 s-12">{{ $quesioner->tahun->tahun }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Indikator :</strong></label>
                                        <label class="col-md-10 s-12">{{ $quesioner->indikator->n_indikator }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Quesioner :</strong></label>
                                        <label class="col-md-10 s-12">{{ $quesioner->question->n_question }}</label>
                                    </div>
                                    <hr>
                                    @foreach ($jawabans as $index => $i)
                                    <input type="hidden" id="answer{{ $index }}" value="{{ $i->answer_id }}">
                                    <input type="hidden" id="bobot{{ $index }}" value="{{ $i->answer->nilai}}">
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Jawaban {{ $index+1 }} :</strong></label>
                                        <label class="col-md-10 s-12">{{ $i->answer->jawaban }} ({{ $i->answer->nilai }})</label>
                                    </div>
                                    @endforeach
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
                                    <input type="hidden" id="id" name="id" value="{{ $quesioner->id }}"/>
                                    <input type="hidden" name="total_jawaban" value="{{ $totalJawaban }}">
                                    <div class="form-row form-inline">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label s-12 col-md-2">Tahun<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-2 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" name="tahun_id" id="tahun_id" autocomplete="off">
                                                        @foreach ($tahuns as $i)
                                                            <option value="{{ $i->id }}">{{ $i->tahun }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mt-1">
                                                <label class="col-form-label s-12 col-md-2">Indikator<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-9 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" name="indikator_id" id="indikator_id" autocomplete="off">
                                                        @foreach ($indikators as $i)
                                                            <option value="{{ $i->id }}">{{ $i->n_indikator }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mt-1">
                                                <label class="col-form-label s-12 col-md-2">Pertanyaan<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-9 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" name="question_id" id="question_id" autocomplete="off">
                                                        @foreach ($questions as $i)
                                                            <option value="{{ $i->id }}">{{ $i->n_question }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <hr>
                                            @foreach ($jawabans as $index => $i)
                                                <input type="hidden" name="tr_quesioner_answer_id{{ $index }}" value="{{ $i->id }}">
                                            @endforeach
                                            @for ($i = 0; $i < $totalJawaban; $i++)
                                            <div class="form-group mt-1">
                                                <label class="col-form-label s-12 col-md-2">Jawaban {{ $i+1 }} <span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-8 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" id="answer_id{{ $i }}" name="answer_id{{ $i }}" autocomplete="off">
                                                        @foreach ($allJawabans as $k)
                                                            <option value="{{ $k->id }}">{{ $k->jawaban }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <span>Bobot : </span><span id="nilai{{ $i }}"></span>
                                                </div>
                                            </div>
                                            @endfor
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
    $('#indikator_id').val("{{$quesioner->indikator_id}}");
    $('#indikator_id').trigger('change.select2');
    $('#question_id').val("{{$quesioner->question_id}}");
    $('#question_id').trigger('change.select2');
    $('#tahun_id').val("{{$quesioner->tahun_id}}");
    $('#tahun_id').trigger('change.select2');

    for (let index = 0; index < {{ $totalJawaban }}; index++) {
        var answer = $('#answer'+index).val();
        var bobot = $('#bobot'+index).val();

        $('#answer_id'+index).val(answer);
        $('#answer_id'+index).trigger('change.select2');

        $('#nilai'+index).html(bobot);
    }

    for (let index = 0; index < {{ $totalJawaban }}; index++) {
        $('#answer_id'+index).on('change', function(){
            val = $(this).val();
            option = "";
            if(val == ""){
                $('#nilai'+index).html(option);
            }else{
                url = "{{ route('kuesioner.getJawaban', ':id') }}".replace(':id', val);
                $.get(url, function(data){
                    console.log(data.nilai)
                    $('#nilai'+index).html(data.nilai);
                }, 'JSON');
            }
        });
    }

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
