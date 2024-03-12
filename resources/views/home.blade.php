@extends('layout.main')
@section('content')
<style>
    /* Add this style to the head of your HTML document or in your CSS file */
    .h-screen {
        height: 100vh;
        /* Set the height to 100% of the viewport height */
        overflow-y: auto;
        /* Enable vertical scrolling */
    }

    .collapsed {
        width: 10px;
        overflow: hidden;
        height: 400px;
    }

    .fixtheheight {
        height: 300px;
    }

    .shadowrss:hover {
        box-shadow: rgba(0, 0, 0, 0.25) 0px 54px 55px, rgba(0, 0, 0, 0.12) 0px -12px 30px, rgba(0, 0, 0, 0.12) 0px 4px 6px, rgba(0, 0, 0, 0.17) 0px 12px 13px, rgba(0, 0, 0, 0.09) 0px -3px 5px;
    }

    body {
        height: 200vh;
        width: 100vw;
        overflow-x: hidden;

    }

    .containerr {
        position: relative;
        width: 320px;
        margin: 100px auto 0 auto;
        perspective: 1000px;
    }

    .carouselll {
        position: absolute;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
        animation: rotate360 60s infinite forwards linear;
    }

    .carouselll__face {
        position: absolute;
        width: 300px;
        height: 187px;
        top: 20px;
        left: 10px;
        right: 10px;
        background-size: cover;
        display: flex;
    }




    .carouselll__face:nth-child(1) {
        background-image: url("https://source.unsplash.com/1000x1000/?rap");
        transform: rotateY(0deg) translateZ(430px);
    }

    .carouselll__face:nth-child(2) {
        background-image: url("https://source.unsplash.com/1000x1000/?events");
        transform: rotateY(40deg) translateZ(430px);
    }

    .carouselll__face:nth-child(3) {
        background-image: url("https://source.unsplash.com/1000x1000/?gangs");
        transform: rotateY(80deg) translateZ(430px);
    }

    .carouselll__face:nth-child(4) {
        background-image: url("https://source.unsplash.com/1000x1000/?Newyork");
        transform: rotateY(120deg) translateZ(430px);
    }

    .carouselll__face:nth-child(5) {
        background-image: url("https://source.unsplash.com/1000x1000/?festival");
        transform: rotateY(160deg) translateZ(430px);
    }

    .carouselll__face:nth-child(6) {
        background-image: url("https://source.unsplash.com/1000x1000/?event");
        transform: rotateY(200deg) translateZ(430px);
    }

    .carouselll__face:nth-child(7) {
        background-image: url("https://source.unsplash.com/1000x1000/?rock");
        transform: rotateY(240deg) translateZ(430px);
    }

    .carouselll__face:nth-child(8) {
        background-image: url("https://source.unsplash.com/1000x1000/?Music");
        transform: rotateY(280deg) translateZ(430px);
    }

    .carouselll__face:nth-child(9) {
        background-image: url("https://source.unsplash.com/1000x1000/?jazz");
        transform: rotateY(320deg) translateZ(430px);
    }



    @keyframes rotate360 {
        from {
            transform: rotateY(0deg);
        }

        to {
            transform: rotateY(-360deg);
        }
    }


    .max-h-80 {
        max-height: 200px;
    }


    @media (max-width: 375px) {
        .categorie {
            display: block;
        }
    }

    @media (min-width: 1440px) {
        .categorie {
            position: fixed;
        }
    }
</style>
<!-- Header Start -->
<div class="back">
    <div class="containerr">
        <div class="carouselll">
            <div class="carouselll__face"><span></span></div>
            <div class="carouselll__face"><span></span></div>
            <div class="carouselll__face"><span></span></div>
            <div class="carouselll__face"><span></span></div>
            <div class="carouselll__face"><span></span></div>
            <div class="carouselll__face"><span></span></div>
            <div class="carouselll__face"><span></span></div>
            <div class="carouselll__face"><span></span></div>
            <div class="carouselll__face"><span></span></div>
        </div>
    </div>

    <div class="container-fluid header bg-white p-0">
        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-6 p-5 mt-lg-5">
                <h1 class="display-5 animated fadeIn mb-4">Find A <span class="text-primary">Perfect Event</span> To Change Your Life</h1>
                <p class="animated fadeIn mb-4 pb-2">Vero elitr justo clita lorem. Ipsum dolor at sed stet
                    sit diam no. Kasd rebum ipsum et diam justo clita et kasd rebum sea elitr.</p>
                <a href="{{route('events')}}" class="btn btn-primary py-3 px-5 me-3 animated fadeIn">Find Events</a>
            </div>
            <div class="col-md-6 animated fadeIn ">
            </div>
        </div>
    </div>
    <!-- Header End -->


    <!-- Search Start -->
    <div class="container-fluid bg-primary mb-5 wow fadeIn" data-wow-delay="0.1s" style="padding: 35px;">
        <div class="container">
            <form action="{{url('events')}}" method="get">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <input type="text" name="search" id="search" class="form-control border-0 py-4" placeholder="Search By Keyword">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select border-0 py-3" name="organizer" onchange="this.form.submit()">
                            <option value="">Event Organizers</option>
                            @foreach ($allOrganizers as $org)
                            <option value="{{$org->id}}">{{$org->org_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select border-0 py-3" name="location" onchange="this.form.submit()">
                            <option value="">Location</option>
                            @foreach ($cities as $city)
                            <option value="{{$city->id}}">{{$city->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select border-0 py-3" name="eventType" onchange="this.form.submit()">
                            <option value="">Event Types</option>
                            @foreach ($eventTypes as $eventType)
                            <option value="{{$eventType->event_type_name}}" @if ($selectedEventType==$eventType->event_type_name) selected @endif>{{$eventType->event_type_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-dark border-0 w-100 py-3">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Search End -->


    <!-- Category Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1 class="mb-3">Categories</h1>
                <p>Eirmod sed ipsum dolor sit rebum labore magna erat. Tempor ut dolore lorem kasd vero ipsum sit eirmod sit. Ipsum diam justo sed rebum vero dolor duo.</p>
            </div>
            <div class="row g-4">
                @foreach($categories as $category)
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <a class="cat-item d-block bg-light text-center rounded p-3" href="{{ route('events', ['eventType' => $category->event_type_name]) }}">
                        <div class="rounded p-4">
                            <div class="icon mb-3">
                                <!-- Image URL from Unsplash API -->
                                <img class="img-fluid" src="https://source.unsplash.com/500x500/?{{$category->event_type_name}}" alt="Event Type Image">
                            </div>

                            <h6>{{ $category->event_type_name }}</h6>
                            <span>{{ $category->events_count }} Events</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>


    <!-- Category End -->


    <!-- About Start -->
    {{-- <div class="container-xxl py-5">
                <div class="container">
                    <div class="row g-5 align-items-center">
                        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                            <div class="about-img position-relative overflow-hidden p-5 pe-0">
                                <img class="img-fluid w-100" src="{{url('Frontend/img/about.jpg')}}">
</div>
</div>
<div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
    <h1 class="mb-4">#1 Place To Find The Perfect Property</h1>
    <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet</p>
    <p><i class="fa fa-check text-primary me-3"></i>Tempor erat elitr rebum at clita</p>
    <p><i class="fa fa-check text-primary me-3"></i>Aliqu diam amet diam et eos</p>
    <p><i class="fa fa-check text-primary me-3"></i>Clita duo justo magna dolore erat amet</p>
    <a class="btn btn-primary py-3 px-5 mt-3" href="">Read More</a>
</div>
</div>
</div>
</div> --}}
<!-- About End -->


<!-- Property List Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-0 gx-5 align-items-end">
            <div class="col-lg-6">
                <div class="text-start mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                    <h1 class="mb-3">UpComing Events</h1>
                    <p>Eirmod sed ipsum dolor sit rebum labore magna erat. Tempor ut dolore lorem kasd vero ipsum sit eirmod sit diam justo sed rebum.</p>
                </div>
            </div>
            <div class="col-lg-6 text-start text-lg-end wow slideInRight" data-wow-delay="0.1s">
                {{-- <form action="{{route('home')}}" method="get"> --}}
                <ul class="nav nav-pills d-inline-flex justify-content-end mb-5">
                    <li class="nav-item me-2">
                        <button name="featured" class="btn btn-outline-primary active" data-bs-toggle="pill" href="#tab-1">Featured</button>
                    </li>
                    <li class="nav-item me-2">
                        <button name="freeEvents" class="btn btn-outline-primary" data-bs-toggle="pill" href="#tab-2" value="P">Paid</button>
                        {{-- onclick="this.form.submit()" --}}
                    </li>
                    <li class="nav-item me-0">
                        <button name="paidEvents" class="btn btn-outline-primary" data-bs-toggle="pill" href="#tab-3" value="F">Free</button>
                    </li>
                </ul>
                {{-- </form> --}}
            </div>
        </div>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="row g-4">
                    @foreach ($events as $event)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="property-item rounded overflow-hidden">
                            <div class="position-relative property-item-display overflow-hidden">
                                <a href="{{url('events')}}/{{$event->event_slug}}"><img class="img-responsive" src="{{url(Custom::eventImagePath($event->event_id))}}" alt="" height="237px" width="100%"></a>
                                @if ($event->event_subscription == 'F')
                                <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">Free</div>
                                @elseif($event->event_subscription == 'P')
                                <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">Paid</div>
                                @endif
                                <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">{{Custom::orgName($event->event_author_id)}}</div>
                            </div>
                            <div class="p-4 pb-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    @if ($event->event_subscription == 'F')
                                    <h5 class="text-primary">Rs.0</h5>
                                    @elseif($event->event_subscription == 'P')
                                    <h5 class="text-primary">Rs.{{$event->event_ticket_price}}</h5>
                                    @endif

                                    <small class="flex-fill text-end border-end py-2"><i class="fa fa-users text-primary me-2"></i>{{Custom::availableSeats($event->event_id)}} Seats Left</small>
                                </div>

                                <a class="d-block h5 mb-2" href="{{url('events')}}/{{$event->event_slug}}">{{$event->event_name}}</a>
                                <p><i class="fa fa-map-marker-alt text-primary me-2"></i>
                                    {{$event->event_address}}, {{Custom::cityName($event->event_location)}}
                                </p>
                            </div>
                            <div class="d-flex border-top">
                                {{-- <small class="flex-fill text-center border-end py-2"><i class="fa fa-users text-primary me-2"></i>{{Custom::availableSeats($event->event_id)}} Seats Left</small> --}}
                                <small class="flex-fill text-center border-end py-2"><i class="fa fas fa-calendar-alt text-primary me-2"></i>{{$event->event_start_date}}</small>
                                <small class="flex-fill text-center py-2"><i class="fa far fa-clock text-primary me-2"></i>{{$event->event_start_time}}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="col-12 text-center wow fadeInUp" data-wow-delay="0.1s">
                        <a class="btn btn-primary py-3 px-5" href="{{route('events')}}">Browse More Events</a>
                    </div>
                </div>
            </div>
            <div id="tab-2" class="tab-pane fade show p-0">
                <div class="row g-4">
                    @foreach ($paidEvents as $event)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="property-item rounded overflow-hidden">
                            <div class="position-relative property-item-display overflow-hidden">
                                <a href="{{url('events')}}/{{$event->event_slug}}"><img class="img-responsive" src="{{url(Custom::eventImagePath($event->event_id))}}" alt="" height="237px" width="100%"></a>
                                @if ($event->event_subscription == 'F')
                                <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">Free</div>
                                @elseif($event->event_subscription == 'P')
                                <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">Paid</div>
                                @endif
                                <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">{{Custom::orgName($event->event_author_id)}}</div>
                            </div>
                            <div class="p-4 pb-0">
                                @if ($event->event_subscription == 'F')
                                <h5 class="text-primary mb-3">Rs.0</h5>
                                @elseif($event->event_subscription == 'P')
                                <h5 class="text-primary mb-3">Rs.{{$event->event_ticket_price}}</h5>
                                @endif
                                <a class="d-block h5 mb-2" href="{{url('events')}}/{{$event->event_slug}}">{{$event->event_name}}</a>
                                <p><i class="fa fa-map-marker-alt text-primary me-2"></i>{{Custom::cityName($event->event_location)}}</p>
                            </div>
                            <div class="d-flex border-top">
                                <small class="flex-fill text-center border-end py-2"><i class="fa fa-users text-primary me-2"></i>{{Custom::availableSeats($event->event_id)}} Left</small>
                                <small class="flex-fill text-center border-end py-2"><i class="fa fas fa-calendar-alt text-primary me-2"></i>{{$event->event_start_date}}</small>
                                <small class="flex-fill text-center py-2"><i class="fa far fa-clock text-primary me-2"></i>{{$event->event_start_time}}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="col-12 text-center">
                        <a class="btn btn-primary py-3 px-5" href="{{route('events')}}">Browse More Property</a>
                    </div>
                </div>
            </div>
            <div id="tab-3" class="tab-pane fade show p-0">
                <div class="row g-4">
                    @foreach ($freeEvents as $event)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="property-item rounded overflow-hidden">
                            <div class="position-relative property-item-display overflow-hidden">
                                <a href="{{url('events')}}/{{$event->event_slug}}"><img class="img-responsive" src="{{url(Custom::eventImagePath($event->event_id))}}" alt="" height="237px" width="100%"></a>
                                @if ($event->event_subscription == 'F')
                                <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">Free</div>
                                @elseif($event->event_subscription == 'P')
                                <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">Paid</div>
                                @endif
                                <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">{{Custom::orgName($event->event_author_id)}}</div>
                            </div>
                            <div class="p-4 pb-0">
                                @if ($event->event_subscription == 'F')
                                <h5 class="text-primary mb-3">Rs.0</h5>
                                @elseif($event->event_subscription == 'P')
                                <h5 class="text-primary mb-3">Rs.{{$event->event_ticket_price}}</h5>
                                @endif
                                <a class="d-block h5 mb-2" href="{{url('events')}}/{{$event->event_slug}}">{{$event->event_name}}</a>
                                <p><i class="fa fa-map-marker-alt text-primary me-2"></i>{{Custom::cityName($event->event_location)}}</p>
                            </div>
                            <div class="d-flex border-top">
                                <small class="flex-fill text-center border-end py-2"><i class="fa fa-users text-primary me-2"></i>{{Custom::availableSeats($event->event_id)}} Left</small>
                                <small class="flex-fill text-center border-end py-2"><i class="fa fas fa-calendar-alt text-primary me-2"></i>{{$event->event_start_date}}</small>
                                <small class="flex-fill text-center py-2"><i class="fa far fa-clock text-primary me-2"></i>{{$event->event_start_time}}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="col-12 text-center">
                        <a class="btn btn-primary py-3 px-5" href="{{route('events')}}">Browse More Property</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Property List End -->


<!-- Locations Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-3">Event Locations</h1>
            <p>Eirmod sed ipsum dolor sit rebum labore magna erat. Tempor ut dolore lorem kasd vero ipsum sit eirmod sit. Ipsum diam justo sed rebum vero dolor duo.</p>
        </div>
        <div class="row g-4">
            @foreach($cities as $city)
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.{{ $loop->index + 1 }}s">
                <div class="team-item rounded overflow-hidden">
                    <div class="position-relative">
                        <img src="https://source.unsplash.com/1000x1000/?{{$city->name}}" alt="{{ $city->name }}" style="width: 100%; height: 175px">
                    </div>
                    <div class="text-center p-4 mt-3">
                        <h5 class="fw-bold mb-0">{{ $city->name }}</h5>
                    </div>
                    <div class="text-center">
                        <h6 class="badge bg-primary p-2"><a class="text-white btn-sm" href="{{ route('events', ['location' => $city->id]) }}">{{ $eventsCountByCity[$city->id]->event_count ?? 0 }} Upcoming Events <i class="fa bi bi-arrow-right"></i></a></h6>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Team End -->






<!-- Testimonial Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <h1 class="mb-3">Our Clients Say!</h1>
            <p>Eirmod sed ipsum dolor sit rebum labore magna erat. Tempor ut dolore lorem kasd vero ipsum sit eirmod sit. Ipsum diam justo sed rebum vero dolor duo.</p>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
            <div class="testimonial-item bg-light rounded p-3">
                <div class="bg-white border rounded p-4">
                    <p>Tempor stet labore dolor clita stet diam amet ipsum dolor duo ipsum rebum stet dolor amet diam stet. Est stet ea lorem amet est kasd kasd erat eos</p>
                    <div class="d-flex align-items-center">
                        <img class="img-fluid flex-shrink-0 rounded" src="{{url('Frontend/img/testimonial-1.jpg')}}" style="width: 45px; height: 45px;">
                        <div class="ps-3">
                            <h6 class="fw-bold mb-1">Client Name</h6>
                            <small>Profession</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonial-item bg-light rounded p-3">
                <div class="bg-white border rounded p-4">
                    <p>Tempor stet labore dolor clita stet diam amet ipsum dolor duo ipsum rebum stet dolor amet diam stet. Est stet ea lorem amet est kasd kasd erat eos</p>
                    <div class="d-flex align-items-center">
                        <img class="img-fluid flex-shrink-0 rounded" src="{{url('Frontend/img/testimonial-2.jpg')}}" style="width: 45px; height: 45px;">
                        <div class="ps-3">
                            <h6 class="fw-bold mb-1">Client Name</h6>
                            <small>Profession</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="testimonial-item bg-light rounded p-3">
                <div class="bg-white border rounded p-4">
                    <p>Tempor stet labore dolor clita stet diam amet ipsum dolor duo ipsum rebum stet dolor amet diam stet. Est stet ea lorem amet est kasd kasd erat eos</p>
                    <div class="d-flex align-items-center">
                        <img class="img-fluid flex-shrink-0 rounded" src="{{url('Frontend/img/testimonial-3.jpg')}}" style="width: 45px; height: 45px;">
                        <div class="ps-3">
                            <h6 class="fw-bold mb-1">Client Name</h6>
                            <small>Profession</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Testimonial End -->

@endsection