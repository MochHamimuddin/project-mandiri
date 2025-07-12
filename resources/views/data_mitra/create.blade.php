@extends('layout.index')

@section('content')<!-- End Page Title -->

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Tambah Mitra</h5>

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('mitra.store') }}" method="POST">
                            @csrf

                            <div class="row mb-3">
                                <label for="nama_perusahaan" class="col-sm-3 col-form-label">Nama Perusahaan</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan"
                                        value="{{ old('nama_perusahaan') }}" required>
                                    @error('nama_perusahaan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="alamat" class="col-sm-3 col-form-label">Alamat</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="pic" class="col-sm-3 col-form-label">PIC</label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="pic" name="pic" required>
                                        <option value="">Pilih PIC</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('pic') == $user->id ? 'selected' : '' }}>
                                                {{ $user->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pic')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan
                                </button>
                                <a href="{{ route('mitra.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <!-- Jika perlu tambahkan script khusus untuk form ini -->
    <script>
        $(document).ready(function() {
            // Inisialisasi select2 jika diperlukan
            $('#pic').select2({
                placeholder: "Pilih PIC",
                allowClear: true
            });
        });
    </script>
@endsection
