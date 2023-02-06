<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>SIKRAMAT | @yield('title')</title>
        <link href="{{ asset('assets/admin/css/styles.css')}}" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
        <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" crossorigin="anonymous" />
        <link rel="icon" type="image/x-icon" href="{{asset('assets/admin/assets/img/logo_prov_bali.png')}}" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="{{ asset('assets/admin/css/select2.css')}}" rel="stylesheet" />
        @stack('css')
    </head>
    <body class="nav-fixed">

        {{-- NAVBAR --}}
        @include('layouts.banjar.navbar')
        
        {{-- SIDEBAR --}}
        @include('layouts.banjar.sidebar')

            <div id="layoutSidenav_content">
                {{-- CONTENT --}}
                @yield('content')
                {{-- END CONTENT --}}

                {{-- FOOTER --}}
                @include('layouts.banjar.footer')
                {{-- END FOOTER --}}

                {{-- LOGOUT FORM --}}
                <form method="GET" id="form-logout" action="" hidden>
                    @csrf
                </form>
                {{-- END LOGOUT FORM --}}

                {{-- MODAL --}}
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
                                        @if(auth()->user()->role == 'kelihan_adat')
                                            <option value="kelihan_adat">Kelihan Adat</option>
                                        @elseif(auth()->user()->role == 'pangliman_banjar')
                                            <option value="pangliman_banjar">Patajuh/Pangliman Banjar Adat</option>
                                        @elseif(auth()->user()->role == 'penyarikan_banjar')
                                            <option value="penyarikan_banjar">Penyarikan/Juru Tulis Banjar Adat</option>
                                        @elseif(auth()->user()->role == 'patengen_banjar')
                                            <option value="patengen_banjar">Patengen/Juru Raksa Banjar Adat</option>
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
                {{-- END MODAL --}}
                
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset('assets/admin/js/scripts.js')}}"></script>
        <script src="{{ asset('assets/admin/assets/demo/datatables-demo.js')}}"></script>
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
                            $('#body_notification').empty();
                            console.log(result);
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

                //SELECT 2
                $("#hak_akses").select2({
                    language: {
                        noResults: function (params) {
                        return "Data tidak ditemukan";
                        }
                    }
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
