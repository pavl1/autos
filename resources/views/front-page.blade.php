@extends('layouts.app')
@section('content')
    <script type="text/javascript">
        window.marks = {!! json_encode($marks) !!};
    </script>
    <div id="app">
        <router-view></router-view>
    </div>
@endsection
