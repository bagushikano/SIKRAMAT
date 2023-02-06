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
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="{{ asset('assets/admin/css/styles.css')}}" rel="stylesheet"/>
        @stack('css')

    </head>
    <body class="nav-fixed">

        {{-- NAVBAR --}}
        @include('layouts.admin.navbar')
        
        {{-- SIDEBAR --}}
        @include('layouts.admin.sidebar')

            <div id="layoutSidenav_content">
                {{-- CONTENT --}}
                @yield('content')
                {{-- END CONTENT --}}

                {{-- FOOTER --}}
                @include('layouts.admin.footer')
                {{-- END FOOTER --}}

                {{-- LOGOUT FORM --}}
                <form method="GET" id="form-logout" action="" hidden>
                    @csrf
                </form>
                {{-- END LOGOUT FORM --}}
            </div>
        </div>
        <script src="//code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="{{ asset('assets/admin/js/scripts.js')}}"></script>

        {{-- ALERT --}}
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
            function alertDanger(title, msg){
                Swal.fire({
                    title: title,
                    text: msg,
                    icon: "error",
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
        </script>
        {{-- END ALERT --}}
        @stack('js')
    </body>
</html>
