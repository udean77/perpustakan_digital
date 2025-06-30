@extends('layouts.app')

@section('content')
    <div class="container">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
    <div class="alert alert-warning">
        {{ session('error') }}
    </div>
    @endif

    <h4 class="mb-4">👤 {{ $user->nama }}</h4>

    <div class="card">
        <div class="card-body">
            {{-- Nav Tabs --}}
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#biodata" role="tab" aria-selected="true">Biodata Diri</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#alamat" role="tab" aria-selected="false">Daftar Alamat</a>
                </li>
            </ul>

            <div class="tab-content">
                {{-- Tab Biodata --}}
                <div id="biodata" class="tab-pane fade show active" role="tabpanel">
                    <div class="row">
                        {{-- Foto Profil --}}
                        <div class="col-md-3 text-center">
                           <img 
                                src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('images/img-default.jpg') }}" 
                                alt="Foto Profil" 
                                class="img-thumbnail rounded-circle mb-3" 
                                style="width: 150px; height: 150px; object-fit: cover;"
                            >

                            <form method="POST" enctype="multipart/form-data" action="{{ route('profile.updatePhoto') }}">
                                @csrf
                                <input type="file" name="photo" class="form-control mb-2" accept=".jpg,.jpeg,.png" required>
                                <button class="btn btn-outline-primary btn-sm w-100">Pilih Foto</button>
                                <small class="d-block mt-2 text-muted">Maks. 10 MB. JPG, JPEG, PNG.</small>
                            </form>

                        </div>

                        {{-- Data Pribadi dan Kontak --}}
                        <div class="col-md-9">
                            <h5>Ubah Biodata Diri</h5>

                            <p>
                                <strong>Nama:</strong> {{ $user->nama }} 
                                <a href="#" class="btn-edit-profile btn btn-link p-0" data-field="nama" data-label="Nama" data-value="{{ $user->nama }}">
                                    Ubah
                                </a>
                            </p>

                            <p>
                                <strong>Tanggal Lahir:</strong> {{ $user->tanggal_lahir ?? 'Belum diisi' }} 
                                <a href="#" class="btn-edit-profile btn btn-link p-0" data-field="tanggal_lahir" data-label="Tanggal Lahir" data-value="{{ $user->tanggal_lahir }}">
                                    Ubah
                                </a>
                            </p>

                            <p>
                                <strong>Jenis Kelamin:</strong> 
                                @if($user->jenis_kelamin === 'L')
                                    Laki-laki
                                @elseif($user->jenis_kelamin === 'P')
                                    Perempuan
                                @else
                                    Belum diisi
                                @endif
                                <a href="#" class="btn-edit-profile btn btn-link p-0" data-field="jenis_kelamin" data-label="Jenis Kelamin" data-value="{{ $user->jenis_kelamin }}">
                                    Ubah
                                </a>
                            </p>

                            <hr>

                            <h5>Ubah Kontak</h5>

                            <p>
                                <strong>Email:</strong> {{ $user->email }} 
                                <a href="#" class="btn-edit-profile btn btn-link p-0" data-field="email" data-label="Email" data-value="{{ $user->email }}">
                                    Ubah
                                </a>
                            </p>

                            <p>
                                <strong>Nomor HP:</strong> {{ $user->hp ?? 'Belum diisi' }} 
                                <a href="#" class="btn-edit-profile btn btn-link p-0" data-field="hp" data-label="Nomor HP" data-value="{{ $user->hp }}">
                                    Ubah
                                </a>
                            </p>
                            <button type="button" class="btn btn-outline-secondary mt-3" data-bs-toggle="modal" data-bs-target="#modalChangePassword">
                                Ubah Kata Sandi
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal Ubah Kata Sandi --}}
                <div class="modal fade" id="modalChangePassword" tabindex="-1" aria-labelledby="modalChangePasswordLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('profile.changePassword.submit') }}">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalChangePasswordLabel">Ubah Kata Sandi</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Kata Sandi Baru</label>
                                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                {{-- Tab Alamat --}}
                <div id="alamat" class="tab-pane fade" role="tabpanel">
                    {{-- Daftar Alamat --}}
                    <div>
                        <h5>Daftar Alamat</h5>

                        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAddAddress">
                            Tambah Alamat Baru
                        </a>

                        @if ($addresses->count() > 0)
                            <ul class="list-group">
                                @foreach ($addresses as $address)
                                    <li class="list-group-item">
                                        <strong>{{ $address->label }}</strong> 
                                        @if($address->is_default) 
                                            <span class="badge bg-success">Default</span> 
                                        @endif
                                        <br>
                                        {{ $address->alamat_lengkap }}<br>
                                        Penerima: {{ $address->nama_penerima }} - {{ $address->no_hp }}<br>

                                        <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditAddress" data-id="{{ $address->id }}" data-label="{{ $address->label }}"data-alamat="{{ $address->alamat_lengkap }}"data-penerima="{{ $address->nama_penerima }}" data-nohp="{{ $address->no_hp }}" data-default="{{ $address->is_default }}"> Edit </a>

                                        <form action="{{ route('address.delete', $address->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus alamat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>Belum ada alamat tersimpan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Profil --}}
<div class="modal fade" id="modalEditProfil" tabindex="-1" aria-labelledby="modalEditProfilLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editFieldForm" method="POST" action="">
            @csrf
            @method('POST')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditProfilLabel">Ubah <span id="modalEditFieldLabel"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="modalInputContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tambah Alamat --}}
<div class="modal fade" id="modalAddAddress" tabindex="-1" aria-labelledby="modalAddAddressLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('address.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Alamat Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="label">Label</label>
                        <input type="text" name="label" id="label" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat_lengkap">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" id="alamat_lengkap" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="nama_penerima">Nama Penerima</label>
                        <input type="text" name="nama_penerima" id="nama_penerima" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_hp">No. HP</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_default" id="is_default" class="form-check-input">
                        <label for="is_default" class="form-check-label">Jadikan alamat default</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Alamat</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-edit-profile');
        const modal = new bootstrap.Modal(document.getElementById('modalEditProfil'));
        const modalTitle = document.getElementById('modalEditFieldLabel');
        const modalInputContainer = document.getElementById('modalInputContainer');
        const form = document.getElementById('editFieldForm');

        editButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const field = this.dataset.field;
                const label = this.dataset.label;
                let value = this.dataset.value || '';

                // Set modal title
                modalTitle.textContent = label;

                // Set form action sesuai field
                form.action = `/profile/update/${field}`;

                // Bangun input form sesuai tipe field
                let inputHTML = '';

                switch(field) {
                    case 'nama':
                    case 'email':
                    case 'hp':
                        inputHTML = `<input type="${field === 'email' ? 'email' : 'text'}" name="${field}" class="form-control" value="${value}" required>`;
                        break;
                    case 'tanggal_lahir':
                        inputHTML = `<input type="date" name="tanggal_lahir" class="form-control" value="${value}" required>`;
                        break;
                    case 'jenis_kelamin':
                        inputHTML = `
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" ${value === 'L' ? 'selected' : ''}>Laki-laki</option>
                                <option value="P" ${value === 'P' ? 'selected' : ''}>Perempuan</option>
                            </select>`;
                        break;
                    default:
                        inputHTML = `<input type="text" name="${field}" class="form-control" value="${value}" required>`;
                }

                modalInputContainer.innerHTML = inputHTML;

                modal.show();
            });
        });
    });
</script>
@endpush
