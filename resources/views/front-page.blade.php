@extends('layouts.app')
@section('content')
    <div id="app">
        <router-view></router-view>
    </div>

    <h2>Восток</h2>
    @include('partials.marks', array('marks' => $marks->east))
    <h2>Запад</h2>
    @include('partials.marks', array('marks' => $marks->west))
@endsection
