<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>SIKRAMAT | @yield('title')</title>
        <link rel="icon" href="{{ asset('assets/admin/assets/img/logo_prov_bali.png') }}" />

        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css" integrity="sha384-/frq1SRXYH/bSyou/HUp/hib7RVN1TawQYja658FEOodR/FQBKVqT9Ol+Oz3Olq5" crossorigin="anonymous"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
        <link href="{{ asset('assets/admin/css/styles.css')}}" rel="stylesheet"/>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
        @stack('css')
    </head>

    <body class="nav-fixed">
        @include('layouts.krama.navbar')

        @include('layouts.krama.sidebar')

        <div id="layoutSidenav_content">
            {{-- START CONTENT --}}
                @yield('content')
            {{-- END CONTENT --}}

            {{-- START FOOTER --}}
                @include('layouts.krama.footer')
            {{-- END FOOTER --}}

            {{-- START LOGOUT FORM --}}
                <form method="GET" id="form-logout" action="" hidden>
                    @csrf
                </form>
            {{-- END LOGOUT FORM --}}
            
            {{-- START SWITCH ROLE MODAL --}}
                <div class="modal fade" id="ganti_hak_akses_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-gray-100">
                                <h5 class="modal-title" id="exampleModalLabel">Ganti Hak Akses</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                    </span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label for="hak_akses" class="form-lable small my-0 text-dark">Pilih Hak Akses<span class="text-danger small">*</span></label>
                                <select class="select-role form-control @error ('hak_akses') is-invalid @enderror" name="hak_akses" id="hak_akses"  style="width: 100%" aria-placeholder="Pilih Hak Akses" required>
                                    @if(auth()->user()->role == 'bendesa')
                                        <option value="bendesa">Bendesa</option>
                                    @elseif(auth()->user()->role == 'pangliman')
                                        <option value="pangliman">Patajuh/Pangliman Desa Adat</option>
                                    @elseif(auth()->user()->role == 'penyarikan')
                                        <option value="penyarikan">Penyarikan/Juru Tulis Desa Adat</option>
                                    @elseif(auth()->user()->role == 'patengen')
                                        <option value="patengen">Patengen/Juru Raksa Desa Adat</option>
                                    @elseif(auth()->guard()->user()->role == 'kelihan_adat')
                                        <option value="kelihan_adat">Kelihan Adat</option>
                                    @elseif(auth()->guard()->user()->role == 'pangliman_banjar')
                                        <option value="pangliman_banjar">Patajuh/Pangliman Banjar Adat</option>
                                    @elseif(auth()->guard()->user()->role == 'penyarikan_banjar')
                                        <option value="penyarikan_banjar">Penyarikan/Juru Tulis Banjar Adat</option>
                                    @elseif(auth()->guard()->user()->role == 'patengen_banjar')
                                        <option value="patengen_banjar">Patengen/Juru Raksa Banjar Adat</option>
                                    @endif
                                        <option value="krama" selected>Krama</option>
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
                            <div class="modal-footer d-flex justify-content-between">
                                <button class="btn btn-danger px-3 py-1" type="button" data-dismiss="modal">
                                    Batal
                                </button>
                                <button class="btn btn-success px-3 py-1" type="button" onclick="ganti_hak_akses()">Selanjutnya</button>
                            </div>
                        </div>
                    </div>
                </div>
            {{-- END SWITCH ROLE MODAL --}}
        </div>

        <script src="//code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset('assets/admin/js/scripts.js')}}"></script>

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
                    $('#ganti_hak_akses_modal').modal('hide');
                }
                else if(hak_akses == 'kelihan_adat' || hak_akses == 'pangliman_banjar' || hak_akses == 'penyarikan_banjar' || hak_akses == 'patengen_banjar'){
                    window.location.replace("{{ route('banjar-dashboard') }}");
                }else if(hak_akses == 'bendesa' || hak_akses == 'pangliman' || hak_akses == 'penyarikan' || hak_akses == 'patengen'){
                    window.location.replace("{{ route('desa-dashboard') }}");
                }
            }

            $(document).ready(function() {
                //NOTIF LISTENER
                $('#banjar-notifikasi-jumlah').on('click', function(){
                    var role = '{{ auth()->guard()->user()->role }}';
                    $('#body_notification').hide();
                    $('#empty_notification').hide();
                    $('#loading_notification').show();
                    var url = "{{ route('get-notifikasi', ":role") }}";
                    url = url.replace(':role', role);
                    jQuery.ajax({
                        url: url,
                        method: 'get',
                        success: function(result){
                            console.log(result);
                            $('#body_notification').empty();
                            if(result.status){
                                //SET NOTIF CONTENT
                                result.data.forEach(element => {
                                    var url = "{{ route('read-notifikasi', ":id") }}";
                                    url = url.replace(':id', element.id);
                                    var konten = '';
                                    konten += '<a class="dropdown-item dropdown-notifications-item" href="'+url+'">';
                                    konten += '<div class="dropdown-notifications-item-icon bg-warning"><i class="text-light font-weight-bold">!</i></div>';
                                    konten += '<div class="dropdown-notifications-item-content">';
                                    konten += '<div class="dropdown-notifications-item-content-details">'+element.converted_created_at+'</div>';
                                    konten += '<div class="dropdown-notifications-item-content-text text-wrap">'+element.konten+'</div>';
                                    konten += '</div></a>';
                                    $('#body_notification').append(konten);
                                });
                                $('#body_notification').append('<a class="dropdown-item dropdown-notifications-footer" href="javascript:void(0);" onclick="read_all_notif()"><i class="fas fa-check mr-1"></i> Tandai semua telah dibaca</a>')
                                $('#loading_notification').hide();
                                $('#body_notification').show(); 
                                
                                //SET JUMLAH NOTIF
                                $('#jumlah_notif_badge').text(result.jumlah_notifikasi);
                            }else{
                                $('#loading_notification').hide();
                                $('#empty_notification').show();
                            }
                        }
                    });
                });
            });

            function read_all_notif(){
                var role = '{{ auth()->guard()->user()->role }}';
                $('#body_notification').hide();
                $('#empty_notification').hide();
                $('#loading_notification').show();
                var url = "{{ route('read-all-notifikasi', ":role") }}";
                url = url.replace(':role', role);
                jQuery.ajax({
                    url: url,
                    method: 'get',
                    success: function(result){
                        if(result.status){
                            $('#loading_notification').hide();
                            $('#empty_notification').show();
                            $('#jumlah_notif_badge').text('0');
                        }else{
                            $('#loading_notification').hide();
                            $('#empty_notification').show();
                        }
                    }
                });
            }
        </script>

        @stack('js')

    </body>
</html>
