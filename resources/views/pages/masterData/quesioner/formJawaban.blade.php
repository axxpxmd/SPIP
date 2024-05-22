@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-question-circle-o mr-2"></i>
                        {{ $title }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li class="nav-item">
                        <a class="nav-link active show" href="{{ route($route.'index') }}"><i class="icon icon-arrow_back"></i>Kembali</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link show" href="{{ route($route.'index') }}"><i class="icon icon-home2"></i>Semua Data</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content my-3" id="pills-tabContent">
            <div class="row">
                <div class="col-md-12">
                    <div id="alert"></div>
                    <div class="card">
                        <h6 class="card-header"><strong>Tambah Data</strong></h6>
                        <div class="card-body">
                            <form class="needs-validation" id="form" method="POST"  enctype="multipart/form-data" novalidate>
                                {{ method_field('POST') }}
                                <input type="hidden" name="tahun_id" value="{{ $tahunId }}">
                                <input type="hidden" name="indikator_id" value="{{ $indikatorId }}">
                                <input type="hidden" name="question_id" value="{{ $questionId }}">
                                <input type="hidden" name="total_jawaban" value="{{ $totalJawaban }}">
                                <div class="form-row form-inline">
                                    <div class="col-md-12">
                                        @for ($i = 0; $i < $totalJawaban; $i++)
                                        <div class="form-group mt-1">
                                            <label class="col-form-label s-12 col-md-2">Jawaban {{ $i+1 }} <span class="text-danger ml-1">*</span></label>
                                            <div class="col-md-8 p-0 bg-light">
                                                <select class="select2 form-control r-0 light s-12" id="answer_id{{ $i }}" name="answer_id{{ $i }}" autocomplete="off">
                                                    <option value="">Pilih Jawaban</option>
                                                    @foreach ($jawabans as $k)
                                                        <option value="{{ $k->id }}" {{ $k->id == $i+1 ? 'selected' : '-' }}>{{ $k->jawaban }} ({{ $k->nilai }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            {{-- <div class="col-md-2">
                                                <span>Bobot : </span><span id="nilai{{ $i }}"></span>
                                            </div> --}}
                                        </div>
                                        @endfor
                                        <div class="form-group mt-2">
                                            <div class="col-md-2"></div>
                                            <button type="submit" class="btn btn-primary btn-sm"><i class="icon-save mr-2"></i>Simpan</button>
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
@endsection
@section('script')
<script type="text/javascript">
    function add(){
        save_method = "add";
        $('#form').trigger('reset');
        $('input[name=_method]').val('POST');
        $('#txtAction').html('');
        $('#reset').show();
        for (let index = 0; index < {{ $totalJawaban }}; index++) {
            $('#answer_id'+index).val("");
            $('#answer_id'+index).trigger('change.select2');
            $('#nilai'+index).html("");
        }
    }

    for (let index = 0; index < {{ $totalJawaban }}; index++) {
        total_jawaban = {{ $totalJawaban }};
        $('#answer_id'+index).on('change', function(){
            val = $(this).val();
            option = "";
            if(val == ""){
                $('#nilai'+index).html(option);
            }else{
                url = "{{ route('kuesioner.getJawaban', ':id') }}".replace(':id', val);
                $.get(url, function(data){
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
            url = "{{ route($route.'store') }}",
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
                                    window.location.href = "{{ route($route.'index') }}";
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
                }
            });
            return false;
        }
        $(this).addClass('was-validated');
    });
</script>
@endsection
