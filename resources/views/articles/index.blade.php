@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @foreach($articles as $article)
            <div class="card">
                <div class="card-header"><a href="{{route('article.show', $article->id)}}">{{$article->title}}</a></div>

                <div class="card-body">
                    {{$article->body}}
                    <form action="{{ route('article.destroy', $article->id)}}" method="post">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" type="submit">Delete</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
