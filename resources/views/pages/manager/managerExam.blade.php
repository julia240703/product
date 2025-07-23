@extends('layouts.appManager')
<title>Data Psikotes</title>
 
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title mb-30">
                  <h2>Data Psikotes</h2>
                </div>
              </div>
              <!-- end col -->
              <div class="col-md-6">
                <div class="breadcrumb-wrapper mb-30">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">
                        <a href="#0">Manager</a>
                      </li>
                      <li class="breadcrumb-item active" aria-current="page">
                        exam question
                      </li>
                    </ol>
                  </nav>
                </div>
              </div>
              <div id="viewContent">
                  <div class="card mb-4">
                      <div class="card-body">
                          {!! $editableContent !!}
                      </div>
                  </div>
              </div>
              <!-- end col -->
            </div>
            <!-- end row -->
          </div>
          <!-- ========== title-wrapper end ========== -->

<div class="row">

  @foreach ($quizzes as $quiz)
    @include('layouts.cardManager', ['quiz' => $quiz])
  @endforeach
  
</div>


          <!-- End Row -->

@endsection