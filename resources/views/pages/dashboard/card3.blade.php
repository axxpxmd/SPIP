<div>
    <div class="tab-content" id="v-pills-tabContent">
        {{-- <div class="card-header white no-b mt-2">
            <h6>PENGGUNA</h6>
        </div> --}}
        <div class="tab-pane animated fadeInUpShort show active" id="v-pills-1">
            <div class="row my-2">
                <div class="col-md-3">
                    <div class="counter-box white r-5 p-3">
                        <div class="p-4">
                            <div class="float-right">
                                <span class="icon icon-user-circle-o text-primary s-48"></span>
                            </div>
                            <div class="counter-title">Total User</div>
                            <h5 class="mt-3 sc-counter" id="totalUser">{{ $totalUser }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="counter-box white r-5 p-3">
                        <div class="p-4">
                            <div class="float-right">
                                <span class="icon icon-school text-warning s-48"></span>
                            </div>
                            <div class="counter-title ">Sekolah</div>
                            <h5 class="mt-3 sc-counter">{{ $sekolah }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="counter-box white r-5 p-3">
                        <div class="p-4">
                            <div class="float-right">
                                <span class="icon icon-building2 red-text s-48"></span>
                            </div>
                            <div class="counter-title">Kelurahan</div>
                            <h5 class="sc-counter mt-3">{{ $kelurahan }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="counter-box white r-5 p-3">
                        <div class="p-4">
                            <div class="float-right">
                                <span class="icon icon-local_hospital green-text s-48"></span>
                            </div>
                            <div class="counter-title">Puskesmas</div>
                            <h5 class="mt-3 sc-counter">{{ $puskesmas }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>