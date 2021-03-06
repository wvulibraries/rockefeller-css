
@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" name="adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('/data',$tblId) }}">{{$tblNme}} Records</a></h1>
      <p>Browse through the records or search here.</p>
    </span>
  </div>
</div>

<!-- Search engine -->
@include('user/searchbox');

<!-- Separation -->
<hr/>

<div class="dataWrppr" role="main">

    <div class="container">
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="rcrdOptnsCrd-top">
          <div class="col-xs-6 text-left">
            @if($rcrds->currentPage() != 1)
            <a class="btn btn-primary left-button" href="{{$rcrds->previousPageUrl()}}">
            @else
            <a class="btn btn-primary left-button disabled" href="{{$rcrds->previousPageUrl()}}">
            @endif
              <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
              <span>previous page</span>
            </a>

          </div>

          <div class="col-xs-6 text-right">
            @if($rcrds->currentPage() != $rcrds->lastPage())
            <a class="btn btn-primary pull-right" href="{{$rcrds->nextPageUrl()}}">
            @else
            <a class="btn btn-primary pull-right disabled" href="{{$rcrds->nextPageUrl()}}">
            @endif
              <span>next page</span>
              <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            </a>
          </div>
        </div>
      </div>
    </div>


    <!-- Separation -->
    <hr/>

    <div class="container">
      <div class="col-xs-12 col-sm-12 col-md-12">
        @foreach($rcrds as $key => $rcrd)
          <div class="dataCard">
            <a href="{{ url('/data', [$tblId, $rcrd->id]) }}">
                @foreach($clmnNmes as $key => $clmnNme)
                  @if($key < 5)
                    <span class="card-items"><strong>{{$clmnNme}}</strong>: {{$rcrd->$clmnNme}}</span>
                  @endif
                @endforeach
            </a>
          </div>
        @endforeach
      </div>

      <div class="col-xs-12 col-sm-12 col-md-12">
        @if(1 <= $rcrds->currentPage() and $rcrds->currentPage() <= $rcrds->lastPage())
          <div class="rcrdOptnsCrd text-center">
            <ul class="pagination pagination-lg">
              @if($rcrds->currentPage() != 1)<li><a href="{{$rcrds->previousPageUrl()}}"><span class="glyphicon glyphicon-chevron-left"></span><span class="sr-only">previous page</span></a></li>@endif
              @if($rcrds->currentPage() != 1)<li><a href="{{$rcrds->url(1)}}">first</a></li>@endif
              @if($rcrds->currentPage()-2 >= 1)<li><a href="{{$rcrds->url($rcrds->currentPage()-2)}}">{{$rcrds->currentPage()-2}}</a></li>@endif
              <li class="active"><a href="{{$rcrds->url($rcrds->currentPage())}}">{{$rcrds->currentPage()}}</a></li>
              @if($rcrds->hasMorePages() and $rcrds->currentPage()+2 <= $rcrds->lastPage())<li><a href="{{$rcrds->url($rcrds->currentPage()+2)}}">{{$rcrds->currentPage()+2}}</a></li>@endif
              @if($rcrds->currentPage() != $rcrds->lastPage())<li><a href="{{$rcrds->url($rcrds->lastPage())}}">last</a></li>@endif
              @if($rcrds->currentPage() != $rcrds->lastPage())<li><a href="{{$rcrds->nextPageUrl()}}"><span class="glyphicon glyphicon-chevron-right"></span><span class="sr-only">next page</span></a></li>@endif
            </ul>
          </div>
        @else
          <p class="text-center">Page {{$rcrds->currentPage()}} doesn't exist.</p>
        @endif
      </div>
    </div>
</div>
@endsection
