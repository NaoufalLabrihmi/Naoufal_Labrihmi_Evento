<?php

namespace App\Http\Controllers;

use App\Custom;
use App\Models\eventReviews;
use App\Models\Events;
use App\Models\Notification;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function index()
    {
        $reviews = eventReviews::all();
        $data = compact('reviews');
        return view('reviews')->with($data);
    }

    public function store(Request $request)
    {
        //    check($request);

        $reviews = new eventReviews;
        $reviews->event_id = $request['event_id'];
        $reviews->user_id = session()->get('user_id');
        $reviews->stars = $request['product_rating'];
        $reviews->description = $request['review_description'];
        $reviews->save();
        $review_id = $reviews->review_id;
        $notification = new Notification;
        $notification->noti_title = 'Review: ' . Custom::authorName(session()->get('user_id')) . ' place a review on your Event ' . Custom::getEventTitle($request['event_id']);
        $notification->noti_for = 'OA';
        $notification->noti_forId = Custom::getEventOrgId($request['event_id']);
        $notification->noti_type = 'Rev';
        $notification->noti_typeId = $review_id;
        $notification->noti_byId = session()->get('user_id');
        $notification->save();
        if ($reviews) {
            return redirect()->route('user_events')->with('success', 'Review Successfully Added');
        } else {
            return redirect()->route('user_events')->with('error', 'Something went wrong');
        }
    }

    public function eventReviews($slug)
    {
        $event = Events::where('event_slug', '=', $slug)->first();

        if (!$event) {
            // Handle the case where the event is not found
            // You can redirect the user or display an error message
        }

        $event_id = $event->event_id;
        $reviews = eventReviews::orderBy('review_id', 'desc')->where('event_id', '=', $event_id)->get();

        // Pass the $event variable to the view
        return view('reviews', compact('event', 'reviews'));
    }



    public function user_reviews()
    {
        $reviews = eventReviews::orderBy('review_id', 'desc')->where('user_id', '=', session()->get('user_id'))->get();
        $data = compact('reviews');
        return view('myReviews')->with($data);
    }
}
