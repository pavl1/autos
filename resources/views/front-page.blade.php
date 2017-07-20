@extends('layouts.app')
@section('content')
    <script type="text/javascript">
        window.marks = {!! json_encode($marks) !!};
    </script>
    <div id="app">
        <transition name="slide-fade" mode="out-in">
            <router-view></router-view>
        </transition>
    </div>
@endsection
