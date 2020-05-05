@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{route('article.store')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="title">Title</label>
                    <input class="form-control {{$errors->has('title')?'is-invalid':''}}" name="title" type="text">
                    @if($errors->has('title'))
                        <p class="text-danger">*{{$errors->first('title')}}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label for="body">Body</label>
                    <input class="form-control {{$errors->has('body')?'is-invalid':''}}" name="body" type="text">
                    @if($errors->has('body'))
                        <p class="text-danger">*{{$errors->first('body')}}</p>
                    @endif
                </div>
                <input type="submit" class="btn btn-primary">
            </form>
        </div>
    </div>
</div>
@endsection
