<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title>SIKRAMAT | @yield('title')</title>
        <link rel="icon" href="{{ asset('assets/admin/assets/img/logo_prov_bali.png') }}" />

        
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css" integrity="sha384-/frq1SRXYH/bSyou/HUp/hib7RVN1TawQYja658FEOodR/FQBKVqT9Ol+Oz3Olq5" crossorigin="anonymous"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="{{ asset('assets/admin/css/styles.css')}}" rel="stylesheet"/>
        @stack('css')
    </head>
    <body class="nav-fixed">

        {{-- NAVBAR --}}
        @include('layouts.desa.navbar')
        
        {{-- SIDEBAR --}}
        @include('layouts.desa.sidebar')

            <div id="layoutSidenav_content">
                {{-- CONTENT --}}
                @yield('content')
                {{-- END CONTENT --}}

                {{-- FOOTER --}}
                @include('layouts.desa.footer')
                {{-- END FOOTER --}}

                {{-- LOGOUT FORM --}}
                <form method="GET" id="form-logout" action="" hidden>
                    @csrf
                </form>
                {{-- END LOGOUT FORM --}}

                <!-- Select Tindakan Modal -->
                <div class="modal fade" id="ganti_hak_akses_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-gray-100">
                                <h5 class="modal-title" id="exampleModalLabel">Ganti Hak Akses</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="hak_akses" class="small">Pilih Hak Akses<span class="text-danger small">*</span></label>
                                    <select class="select2 custom-select @error ('hak_akses') is-invalid @enderror" name="hak_akses" id="hak_akses"  style="width: 100%" aria-placeholder="Pilih Hak Akses" required>
                                        @if(auth()->user()->role == 'bendesa')
                                            <option value="bendesa">Bendesa</option>
                                        @elseif(auth()->user()->role == 'pangliman')
                                            <option value="pangliman">Patajuh/Pangliman Desa Adat</option>
                                        @elseif(auth()->user()->role == 'penyarikan')
                                            <option value="penyarikan">Penyarikan/Juru Tulis Desa Adat</option>
                                        @elseif(auth()->user()->role == 'patengen')
                                            <option value="patengen">Patengen/Juru Raksa Desa Adat</option>
                                        @endif
                                            <option value="krama">Krama</option>
                                    </select>
                                    @error('hak_akses')
                                        <div class="invalid-feedback text-start">
                                            {{ $message }}
                                        </div>
                                    @else
                                        <div class="invalid-feedback">
                                            Hak Akses wajib dipilih
                                        </div>
                                    @enderror 
                                </div>
                            </div>
                            <div class="modal-footer"><button class="btn btn-danger" type="button" data-dismiss="modal">Batal</button><button class="btn btn-success" type="button" onclick="ganti_hak_akses()">Selanjutnya</button></div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <script src="//code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="{{ asset('assets/admin/js/scripts.js')}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            function alertSuccess(title, msg){
                Swal.fire({
                    title: title,
                    text: msg,
                    icon: "success",
                });
            }

            function alertError(title, msg){
                Swal.fire({
                    title: title,
                    text: msg,
                    icon: "warning",
                });
            }

            function logout(){
                Swal.fire({
                title: 'Logout',
                text: "Apakah anda yakin ingin keluar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#form-logout").attr('action', '{{ route('logout') }}');    
                        $("#form-logout").submit();  
                    }
                })
            }

            function ganti_hak_akses_modal(){
                $('#ganti_hak_akses_modal').modal('show');
            }

            function ganti_hak_akses(){
                let hak_akses = $('#hak_akses').val();
                if(hak_akses == 'krama'){
                    window.location.replace("{{ route('Dashboard Krama') }}");
                }else{
                    $('#ganti_hak_akses_modal').modal('hide');
                }
            }

            $(document).ready(function() {
                //SELECT 2
                $(".custom-select").select2({
                    language: {
                        noResults: function (params) {
                        return "Data tidak ditemukan";
                        }
                    }
                });
            });
        </script>
        @stack('js')
    </body>
</html>
