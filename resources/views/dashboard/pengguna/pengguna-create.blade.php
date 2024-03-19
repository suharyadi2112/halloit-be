@extends('partial.layout.main')
@section('title', 'Profil')
@section('content')
<div class="page-inner">
	<h4 class="page-title">Profil</h4>
	<div class="row">
		<div class="col-md-12">
			<div class="card card-with-nav">
				<div class="card-header">
					<h4 class="card-title">Buat Profil Baru</h4>
				</div>
				<div class="card-body">
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


					<form action="{{ route('pengguna.store') }}" method="post">
						@csrf
						@method('POST')
						<div class="form-group">
							<label for="name">Nama <span class="required-label">*</span></label>
							<input type="text" class="form-control" id="name" name="name" placeholder="Nama" required value="{{ old('name') }}"/>
						</div>
                        <div class="form-group">
							<label for="handphone">No Handphone <span class="required-label">*</span></label>
							<input type="text" class="form-control" id="handphone" name="handphone" placeholder="No Handphone" required value="{{ old('handphone') }}"/>
						</div>
						<div class="form-group">
							<label for="email">Email <span class="required-label">*</span></label>
							<input type="email" class="form-control" id="email" name="email" placeholder="Email" required value="{{ old('email') }}"/>
						</div>
						<div class="form-group">
							<label for="password">Password <span class="required-label">*</span></label>
							<input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
						</div>
                        <div class="form-group">
                            @php
                                $jabatan =[
                                    'IT Support' => 'IT Support',
                                    'Guest' => 'Guest'
                                ];
                            @endphp
							<label for="jabatan">Jabatan <span class="required-label">*</span></label>
							<select class="form-control" name="jabatan" id="jabatan" required>
                                <option value="">Pilih Jabatan</option>
                                @foreach ($jabatan as $key => $value)
                                    <option value="{{ $key }}" {{ old('jabatan') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
						</div>
                        <div class="form-group">
							<label for="divisi">Divisi <span class="required-label">*</span></label>
                            <input type="text" class="form-control" id="divisi" name="divisi" placeholder="Divisi" required value="{{ old('divisi') }}">    
						</div>
						<div class="text-right mt-3 mb-3">
							<a href="{{ route('pengguna.index') }}" type="submit" class="btn btn-sm btn-secondary">Kembali</a>
							<button type="submit" class="btn btn-sm btn-primary">Simpan</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
	</div>
</div>
@endsection