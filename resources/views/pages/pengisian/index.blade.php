@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-document-text"></i>
                        {{ $title }}
                    </h4>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        @if (count($errors) > 0)
        <div class="alert alert-danger mt-2" id="errorAlert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Whoops Error!</strong>&nbsp;
            <span>You have {{ $errors->count() }} error</span>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="tab-content my-3" id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div id="alert"></div>
                        <div class="card">
                            <div class="card-body">
                                <form class="needs-validation" action="{{ route($route.'create') }}" method="GET"  novalidate>
                                    <div class="form-row form-inline">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="col-form-label s-12 col-md-2">Tahun</label>
                                                <div class="col-md-6 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" name="tahun_id" id="tahun_id" autocomplete="off">
                                                        <option value="0">Pilih</option>
                                                        @foreach ($tahuns as $i)
                                                            <option value="{{ $i->id }}">{{ $i->tahun }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mt-2">
                                                <div class="col-md-2"></div>
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="icon-arrow_forward mr-2"></i>Selanjutnya</button>
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
     $(document).ready(function() {
        $("#errorAlert").fadeTo(5000, 1000).slideUp(1000, function() {
            $("#errorAlert").slideUp(1000);
        });
    });
</script>
@endsection
