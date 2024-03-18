@extends('partial.layout.main')
@section('title', 'Detail Pengaduan')
@section('content')
<div class="page-inner">
    <h4 class="page-title">Detail Pengaduan</h4>
        @if ($errors->any())
            <div class="alert alert-danger  alert-dismissible fade show" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="text-danger">{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (session('success'))
        <div class="alert alert-success  alert-dismissible fade show" role="alert">
            <div class="d-flex justify-content-between">
                <div>
                    {{ session('success') }}
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Form Laporan</div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="kode_laporan">Kode Laporan</label>
                        <input type="text" class="form-control" id="kode_laporan" name="kode_laporan" placeholder="Kode Laporan" required value="{{$pengaduan->kode_laporan }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_pelapor">Tanggal Pelapor</label>
                        <input type="text" class="form-control" id="tanggal_pelapor" name="tanggal_pelapor" placeholder="Tanggal Pelapor" required value="{{$pengaduan->tanggal_pelaporan }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="name">Pelapor</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Pelapor" required value="{{ $pengaduan->pelapor->name }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="nomor_handphone">Nomor Telepon</label>
                        <input type="number" class="form-control" id="nomor_handphone" name="nomor_handphone" placeholder="Nomor Handphone" required value="{{ $pengaduan->nomor_handphone }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="lantai">Lantai</label>
                        <input type="number" class="form-control" id="lantai" name="lantai" placeholder="Lantai" required value="{{ $pengaduan->lantai }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="lokasi">Lokasi</label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Lokasi" required value="{{ $pengaduan->lokasi }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="lokasi">Judul Pengaduan</label>
                        <input type="text" class="form-control" id="judul_pengaduan" name="judul_pengaduan" placeholder="Judul Pengaduan" required value="{{ $pengaduan->judul_pengaduan }}" disabled>
                    </div>
                    <div class="form-group">
                        <label for="dekskripsi_pelaporan">Deskripsi</label>
                        <textarea id="dekskripsi_pelaporan" name="dekskripsi_pelaporan" class="form-control" rows="5" placeholder="Deskripsi Pengaduan" required disabled>{{ $pengaduan->dekskripsi_pelaporan }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="dekskripsi_pelaporan">Gambar Kondisi Pengaduan</label>
                        <div class="row image-gallery">
                            @foreach ($gambarPengaduan as $key => $value)
                                <a href="{{ asset('storage/'.$value->picture) }}" class="col-6 col-md-3 mb-4" data-toggle="lightbox">
                                    <img src="{{ asset('storage/'.$value->picture) }}" class="img-fluid" style="width: 100%; object-fit: cover; height: 100px;">
                                </a>   
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select class="form-control" id="kategori_pengaduan_id" name="kategori_pengaduan_id" disabled>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoriPengaduan as $key => $value)
                                <option value="{{ $value->id }}" {{ $pengaduan->kategori_pengaduan_id == $value->id ? 'selected' : '' }}>{{ $value->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Prioritas</label>
                        <select class="form-control" id="prioritas" name="prioritas" disabled>
                            <option value="">-- Pilih Prioritas --</option>
                            <option value="Rendah" {{ $pengaduan->prioritas == 'Rendah' ? 'selected' : '' }}>Rendah</option>
                            <option value="Sedang" {{ $pengaduan->prioritas == 'Sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="Tinggi" {{ $pengaduan->prioritas == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Indikator Mutu</label>
                        <select class="form-control" id="indikator_mutu_id" name="indikator_mutu_id" disabled>
                            @foreach ($indikatorMutu as $key => $value)
                                <option value="{{ $value->id }}" {{ $value->id == $pengaduan->indikator_mutu_id ? 'selected' : '' }}>{{ $value->nama_indikator }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <form action="{{ route('pengaduan.index.update', $pengaduan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Kondisi Perbaikan</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Status Pelaporan <span class="required-label">*</span></label>
                            @php
                            $status = [
                                'waiting' => 'Menunggu',
                                'progress' => 'Proses',
                                'done' => 'Selesai',
                            ];
                            @endphp
                            <select class="form-control" id="status_pelaporan" name="status_pelaporan">
                                <option value="">-- Pilih Status Pelaporan --</option>
                                @foreach ($status as $key => $value)
                                    <option value="{{ $key }}" {{ $key == $pengaduan->status_pelaporan ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group select2-input">
                            <label>Ditugaskan Kepada</label>
                            <select class="form-control" id="workers" name="workers[]" multiple required style="width: 100%">
                                <option value="">-- Pilih Orang --</option>
                                @foreach ($workers as $key => $value)
                                    <option value="{{ $value->id }}" {{ in_array($value->id, $pengaduan->workers->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="imageFixing">
                            <label for="dekskripsi_pelaporan">Kondisi Setelah Perbaikan</label>
                            <input type="file" class="form-control" id="imageInput" name="picture_post[]" multiple>
                            <div id="preview" class="mt-3 row"></div>
                            <button type="button" class="btn btn-sm btn-danger mt-3" id="btnDelete" style="display:none;">Hapus Gambar</button>
                            <div class="row image-gallery">
                                @foreach ($gambarPerbaikanPengaduan as $key => $value)
                                    <a href="{{ asset('storage/'.$value->picture) }}" class="col-6 col-md-3 mb-4" data-toggle="lightbox">
                                        <img src="{{ asset('storage/'.$value->picture) }}" class="img-fluid" style="width: 100%; object-fit: cover; height: 100px;">
                                    </a>   
                                @endforeach
                            </div>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" id="stayPaged" name="stayPaged">
                                <span class="form-check-sign">Tetap dihalaman ini</span>
                            </label>
                        </div>
                    </div>
                    <div class="card-action">
                        <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-black">Kembali</a>
                        <button class="btn btn-sm btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link href="{{ asset('/assets/js/plugin/ekko-lightbox/ekko-lightbox.min.css') }}" rel="stylesheet" />  

@endpush
@push('script')
<script src="{{ asset('/assets/js/plugin/ekko-lightbox/ekko-lightbox.min.js') }}"></script>
<script src="{{ asset('/assets/js/plugin/select2/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $(document).on("click", '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });
        $("#workers").select2({
            placeholder: "Pilih Orang",
            theme: 'bootstrap',
        });

        var statusPelaporan = $('#status_pelaporan');
        var inputGambar = $('#imageFixing');
        inputGambar.hide();
        function toggleInputGambar() {
            if (statusPelaporan.val() === 'done') {
                inputGambar.show();
            } else {
                inputGambar.hide();
            }
        }
        toggleInputGambar();
        statusPelaporan.change(function() {
            toggleInputGambar();
        });
    });
</script>
@endpush