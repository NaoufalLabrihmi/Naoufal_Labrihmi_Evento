<?php

namespace App\Http\Controllers;

use App\Custom;
use App\Mail\TicketValidated;
use App\Models\Admin;
use App\Models\BuyTickets;
use App\Models\Cities;
use App\Models\EventImages;
use App\Models\eventReviews;
use App\Models\Events;
use App\Models\EventType;
use App\Models\Followers;
use App\Models\Notification;
use App\Models\Organizer;
use App\Models\User;
use App\Models\Users;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Charts\EventChart;
use App\Charts\UserChart;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $events = Events::orderBy('event_id', 'desc')
            ->where('event_status', '=', 1) // Only approved events
            ->where('approved', 1) // Check if the event is approved
            ->paginate(6);

        $paidEvents = Events::where('event_subscription', '=', 'P')
            ->where('event_status', '=', 1) // Only approved events
            ->orderBy('event_id', 'desc')
            ->where('approved', 1) // Check if the event is approved
            ->paginate(5);

        $freeEvents = Events::where('event_subscription', '=', 'F')
            ->where('event_status', '=', 1) // Only approved events
            ->orderBy('event_id', 'desc')
            ->where('approved', 1) // Check if the event is approved
            ->paginate(5);

        $eventCount = "";
        $categories = EventType::withCount('events')->get();

        // Retrieve all event images
        $eventImages = EventImages::all();

        // Count events for each location
        // $islamabadEvents = Events::where('event_location', '=', 6)->get();
        // $islamabad = $islamabadEvents->where('event_start_date', '>', date('Y-m-d'))->count();
        // $lahoreEvents = Events::where('event_location', '=', 14)->get();
        // $lahore = $lahoreEvents->where('event_start_date', '>', date('Y-m-d'))->count();
        // $karachiEvents = Events::where('event_location', '=', 2)->get();
        // $karachi = $karachiEvents->where('event_start_date', '>', date('Y-m-d'))->count();
        // $multanEvents = Events::where('event_location', '=', 38)->get();
        // $multan = $multanEvents->where('event_start_date', '>', date('Y-m-d'))->count();

        // Retrieve all organizers
        $allOrganizers = Users::where('user_type', '=', 'OA')->get();

        // Retrieve all cities
        $cities = Cities::all();

        // Retrieve the selected event type from the request
        $selectedEventType = $request->input('eventType', '');

        // Retrieve all event types
        $eventTypes = EventType::all();
        // Count events for each city
        $eventsCountByCity = Events::select('event_location', DB::raw('COUNT(*) as event_count'))
            ->groupBy('event_location')
            ->get()
            ->keyBy('event_location');

        $data = compact(
            'events',
            'paidEvents',
            'freeEvents',
            'eventImages',
            'allOrganizers',
            'cities',
            'eventTypes',
            'selectedEventType',
            'categories',
            'eventsCountByCity'
        );

        return view('home')->with($data);
    }





    public function admin()
    {
        $totalUsers = User::where('user_type', 'U')->count() + Admin::where('user_type', 'U')->count() + Organizer::where('user_type', 'U')->count();
        $totalOrganizers = Organizer::where('user_type', 'OA')->count() + User::where('user_type', 'OA')->count() + Admin::where('user_type', 'OA')->count();
        $totalEvents = Events::count();
        $totalEventTypes = EventType::count();
        $eventTypes = EventType::all();
        $events = Events::all();
        $users = User::all();
        $organizers = Organizer::all();




        return view('admin.dashboard', compact('totalUsers', 'totalOrganizers', 'totalEvents', 'totalEventTypes', 'eventTypes', 'events'));
    }


    public function admin_notifications()
    {
        $allNotifications = Notification::orderBy('noti_id', 'desc')->paginate(10);
        $data = compact('allNotifications');
        return view('admin.notifications')->with($data);
    }

    public function eventDetails($slug)
    {
        $events = Events::where('event_slug', '=', $slug)->first();
        $eventId = $events->event_id;
        $eventImages = EventImages::where('event_list_id', '=', $eventId)->get();
        $totalReviews = eventReviews::where('event_id', '=', $eventId)->count();
        $city = Cities::find($events->event_location);

        $data = compact('events', 'eventImages', 'totalReviews', 'city');

        return view('eventDetails')->with($data);
    }


    public function buyEventTicket($slug, Request $request)
    {
        $request->validate([
            'quantity' => 'required|max:1',
        ]);
        $quantity = $request['quantity'];
        $events = Events::where('event_slug', '=', $slug)->first();
        $eventId = $events->event_id;
        $eventImages = EventImages::where('event_list_id', '=', $eventId)->first();
        $checkUserId = session()->get('user_id');
        if ($checkUserId > 0) {
            $data = compact('events', 'eventImages', 'quantity');
            return view('buyTicket')->with($data);
        } else {
            return redirect()->route('login')->with('error', 'You must be login to buy this ticket');
        }
    }

    public function generateQRCode($ticket)
    {
        $event = Events::find($ticket->buyer_event_id);

        // Check if $event is null
        if ($event) {
            // Generate QR code content
            $qrCodeContent = "Name: " . $ticket->buyer_user_name . "\n";
            $qrCodeContent .= "Email: " . $ticket->buyer_user_email . "\n";
            $qrCodeContent .= "Event: " . $event->event_name;

            $from = [255, 0, 0];
            $to = [0, 0, 255];

            // Generate QR code image
            $qrCode = QrCode::size(100)
                ->style('dot')
                ->eye('circle')
                ->gradient($from[0], $from[1], $from[2], $to[0], $to[1], $to[2], 'diagonal')
                ->generate($qrCodeContent);

            return $qrCode;
        } else {
            // Event not found, handle the error as needed
            return "Event not found";
        }
    }

    public function buyEventTicket_details(Request $request)
    {
        $request->validate([
            'buyerName' => 'required',
            'buyerCnic' => 'required',
            'buyerTicketPrice' => 'required',
            'buyerEmail' => 'required',
        ]);

        $quantity = $request['quantity'];

        $newCategoryArray = [
            "buyer_user_name" => $request->input('buyerName', []),
            "buyer_user_cnic" =>  $request->input('buyerCnic', []),
            "buyer_user_payment_method" => $request['buyerPaymentMethod'],
            "buyer_user_ticket_price" => $request['buyerTicketPrice'],
            "buyer_user_email" => $request->input('buyerEmail', []),
            "buyer_user_id" => session()->get('user_id'),
            "buyer_event_id" => $request['buyerEventId'],
            "buyer_event_author_id" => $request['buyerEventAuthorId'],
        ];

        // Determine the event's reservation method
        $event = Events::find($request['buyerEventId']);
        $reservationMethod = $event->event_reservation_method;

        // Set validation column based on reservation method
        $validation = ($reservationMethod == 'automatic') ? 1 : 0;

        $ticketId = [];

        foreach ($request->input('buyerName', []) as $key => $value) {
            $buyTicket = new BuyTickets;
            $buyTicket->buyer_user_name = $newCategoryArray['buyer_user_name'][$key];
            $buyTicket->buyer_user_email = $newCategoryArray['buyer_user_email'][$key];
            $buyTicket->buyer_user_cnic = $newCategoryArray['buyer_user_cnic'][$key];

            if ($request['buyerPaymentMethod'] != "") {
                $buyTicket->buyer_user_payment_method = $request['buyerPaymentMethod'];
                $buyTicket->buyer_user_payment_status = 'UP';
            } else {
                $buyTicket->buyer_user_payment_method = 'N/A';
                $buyTicket->buyer_user_payment_status = 'P';
            }

            $buyTicket->buyer_user_ticket_price = $newCategoryArray['buyer_user_ticket_price'];
            $buyTicket->buyer_user_id = $newCategoryArray['buyer_user_id'];
            $buyTicket->buyer_event_id = $newCategoryArray['buyer_event_id'];
            $buyTicket->buyer_event_author_id = $newCategoryArray['buyer_event_author_id'];

            $buyTicket->validation = $validation; // Set the validation status

            $buyTicket->save();
            $ticketId[] = $buyTicket->buy_ticket_id;



            // Create notification
            $notification = new Notification;
            $notification->noti_title = 'Ticket Sold: ' . $buyTicket->buyer_user_name . ' bought a ticket for ' . Custom::getEventTitle($buyTicket->buyer_event_id);
            $notification->noti_for = 'OA';
            $notification->noti_forId = $buyTicket->buyer_event_author_id;
            $notification->noti_type = 'T';
            $notification->noti_typeId = $buyTicket->buyer_event_id;
            $notification->noti_byId = session()->get('user_id');
            $notification->save();
        }

        if ($buyTicket) {
            // If validation is automatic, send confirmation message
            if ($validation == 1) {
                foreach ($ticketId as $id) {
                    $ticket = BuyTickets::find($id);
                    $qrCode = $this->generateQRCode($ticket);
                    if ($ticket->buyer_user_email) {
                        Mail::to($ticket->buyer_user_email)->send(new TicketValidated($ticket, $qrCode, $event));
                    }
                }
            }
            if ($request['payNow'] == 'P') {
                $eventData = Events::where('event_id', '=', $request['buyerEventId'])->first();
                $eventId = $eventData->event_id;
                $eventImages = EventImages::where('event_list_id', '=', $eventId)->first();
                $payMethod = $request['buyer_user_payment_method'];
                $totalAmount = $request['totalAmount'];
                $formUrl = 'payment_confirmed';
                $singleTicketId = BuyTickets::where('buy_ticket_id', '=', $ticketId)->first();
                $data = compact('eventData', 'eventImages', 'payMethod', 'ticketId', 'singleTicketId', 'totalAmount', 'formUrl');
                return view('payment')->with($data);
            } else {
                foreach ($ticketId as $key => $value) {
                    $newtickets[] = BuyTickets::where('buy_ticket_id', '=', $ticketId)->first();
                }
                $totalTickets = count($ticketId);
                $eventData = Events::where('event_id', '=', $request['buyerEventId'])->first();
                $totalAmount = $request['totalAmount'];
                $data = compact('eventData', 'totalAmount', 'newtickets', 'totalTickets');
                return view('buyingSuccess')->with($data);
            }
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong');
        }
    }

    public function downloadTicketPDF($id)
    {
        $ticket = BuyTickets::find($id);
        $event = Events::find($ticket->buyer_event_id);
        $qrCode = $this->generateQRCode($ticket);

        if (!$ticket) {
            abort(404);
        }
        $qrCode = $this->generateQRCode($ticket); // This line generates the QR code

        // Generate the PDF content
        $pdfContent = view('ticket_pdf', compact('ticket', 'event', 'qrCode'))->render();

        // Create Dompdf instance
        $dompdf = new Dompdf();

        // Load HTML content into Dompdf
        $dompdf->loadHtml($pdfContent);

        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (generate)
        $dompdf->render();

        // Output the generated PDF to the browser
        return $dompdf->stream("ticket_{$id}.pdf");
    }


    public function payment_confirmed(Request $request)
    {
        // check($request['ticketId']);
        //  checkArray($eventData);
        foreach ($request['ticketId'] as $key => $value) {
            $tickets[] = BuyTickets::where('buy_ticket_id', '=', $value)->first();
            $tickets[$key]->buyer_user_payment_status = 'P';
            $tickets[$key]->update();
        }
        $totalTickets = count($tickets);
        $eventData = Events::where('event_id', '=', $request['event'])->first();
        foreach ($request['ticketId'] as $key => $value) {
            $newtickets[] = BuyTickets::where('buy_ticket_id', '=', $value)->first();
        }
        $totalAmount = $request['totalAmount'];
        $data = compact('eventData', 'newtickets', 'totalTickets', 'totalAmount');
        return view('buyingSuccess')->with($data);
    }

    public function late_eventPyment($slug, $id)
    {
        $eventData = Events::where('event_slug', '=', $slug)->first();
        $singleTicketId = BuyTickets::where('buy_ticket_id', '=', $id)->first();
        $ticketId = BuyTickets::where('buy_ticket_id', '=', $id)->first();
        // checkArray($ticketId);
        $eventImages = EventImages::where('event_list_id', '=', $eventData->event_id)->first();
        $totalAmount = $singleTicketId->buyer_user_ticket_price;
        $formUrl = 'events/payment/' . $slug . '/' . $id;
        $data = compact('eventData', 'singleTicketId', 'ticketId', 'totalAmount', 'eventImages', 'formUrl');
        return view('payment')->with($data);
    }

    public function update_latePayment(Request $request)
    {
        $ticket = BuyTickets::where('buy_ticket_id', '=', $request['singleticketId'])->first();
        $ticket->buyer_user_payment_status = 'P';
        $ticket->update();
        $eventData = Events::where('event_id', '=', $request['event'])->first();
        // $newtickets = BuyTickets::where('buy_ticket_id', '=', $ticket->buy_ticket_id)->first();
        $totalTickets = '1';
        $newtickets[] = $ticket;
        // check($newtickets);
        $totalAmount = $request['totalAmount'];
        $data = compact('eventData', 'newtickets', 'totalTickets', 'totalAmount');
        return view('buyingSuccess')->with($data);
    }

    public function buyingSuccess()
    {
        return view('buyingSuccess');
    }

    public function dashboard()
    {


        $user_id = session()->get('user_id');
        $eventList = Events::orderBy('event_id', 'desc')->where('event_author_id', '=', $user_id)->get();
        $date = date('Y-m-d');
        $completedTotalEvents = $eventList->where('event_start_date', '<', $date)->count();
        $upcommingTotalEvents = $eventList->where('event_start_date', '>', $date)->count();

        //Event Count and Listing
        $totalEvents = Events::where('event_author_id', '=', $user_id)->count();
        $eventList = Events::orderBy('event_id', 'desc')->where('event_author_id', '=', $user_id)->paginate(3);
        $eventId = $eventList->pluck('event_author_id')->first();

        $soldTickets = BuyTickets::orderBy('buy_ticket_id', 'desc')->where('buyer_event_author_id', '=', $eventId)->paginate(3);
        $soldTicketsCount = BuyTickets::orderBy('buy_ticket_id', 'desc')->where('buyer_event_author_id', '=', $eventId)->count();
        $ticketEarnings = $soldTickets->pluck('buyer_user_ticket_price');
        $totalEarnings = 0;
        foreach ($ticketEarnings as $key => $value) {
            $totalEarnings += $value;
        }
        // echo '<pre>';
        // // print_r($soldTickets);
        // print_r($soldTickets->toArray());
        // die;
        // $CompletedEvents = $eventList->where('event_end_date', '<', date('Y-m-d'))->get();
        $data = compact('eventList', 'totalEvents', 'soldTickets', 'soldTicketsCount', 'totalEarnings', 'completedTotalEvents', 'upcommingTotalEvents');
        return view('dashboard')->with($data);
    }
    public function events(Request $request)
    {
        $search = $request['search'] ?? "";
        $organizer = $request['organizer'] ?? "";
        $location = $request['location'] ?? "";
        $locationUpcoming = $request['locationUpcoming'] ?? "";
        $eventType = $request['eventType'] ?? ""; // Add this line

        $cacheKey = 'events_' . md5(serialize([$search, $organizer, $location, $locationUpcoming, $eventType]));

        $events = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($search, $organizer, $location, $locationUpcoming, $eventType) {
            return Events::orderBy('event_id', 'desc')
                ->where('approved', 1) // Check if the event is approved
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where("event_name", "LIKE", "%$search%")
                            ->orWhere("event_slug", "LIKE", "%$search%");
                    });
                })
                ->when($organizer, function ($query) use ($organizer) {
                    $query->where("event_author_id", "=", $organizer);
                })
                ->when($location, function ($query) use ($location) {
                    $query->where("event_location", "=", $location);
                })
                ->when($locationUpcoming, function ($query) use ($locationUpcoming) {
                    $query->where('event_location', '=', $locationUpcoming)
                        ->where('event_start_date', '>', date('Y-m-d'));
                })
                ->when($eventType, function ($query) use ($eventType) { // Add this condition
                    $query->whereHas('eventType', function ($query) use ($eventType) {
                        $query->where('event_type_name', '=', $eventType);
                    });
                })
                ->paginate(6); // Pagination added here
        });

        $eventCount = $events->total();

        // Retrieve the selected event type from the request
        $selectedEventType = $request->input('eventType', '');

        $cities = Cities::all();
        $allOrganizers = Users::where('user_type', '=', 'OA')->get();
        $eventTypes = EventType::all(); // Fetch all event types

        $data = compact('events', 'cities', 'location', 'allOrganizers', 'search', 'eventCount', 'organizer', 'eventTypes', 'selectedEventType');
        return view('events')->with($data);
    }


    public function userProfile()
    {
        $user_id = session()->get('user_id');
        $users = Users::find($user_id);
        $data = compact('users', 'user_id');
        return view('userProfile')->with($data);
    }

    public function updateUserProfile(Request $request)
    {
        $request->validate([
            'userName' => 'required',
            'userEmail' => 'required|email',
        ]);
        $user_id = session()->get('user_id');
        $user = Users::find($user_id);
        $user->name = $request['userName'];
        $user->email = $request['userEmail'];
        $user->address = $request['userAddress'];
        $user->contact = $request['userContact'];
        $user->facebook = $request['facebook'];
        $user->instagram = $request['instagram'];
        $user->twitter = $request['twitter'];
        $user->youtube = $request['youtube'];
        if ($request->file('userImage') != "") {
            $imageName = time() . "." . $request->file('userImage')->extension();
            $request->file('userImage')->move(public_path('Backend/users_images'), $imageName);
            $user->image = $imageName;
            $user->avatar = $imageName;
        }
        $user->update();
        if ($user) {
            return redirect()->back()->with('success', 'Profile Updated Successfully');
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong');
        }
    }

    public function viewUserProfile($id)
    {
        $users = Users::find($id);
        $user_id = $id;
        $data = compact('users', 'user_id');
        return view('userProfile')->with($data);
    }

    public function admin_updateUserProfile($id, Request $request)
    {
        $request->validate([
            'userName' => 'required',
            'userEmail' => 'required|email',
        ]);
        $user_id = $id;
        $user = Users::find($user_id);
        $user->name = $request['userName'];
        $user->email = $request['userEmail'];
        $user->address = $request['userAddress'];
        $user->contact = $request['userContact'];
        $user->facebook = $request['facebook'];
        $user->instagram = $request['instagram'];
        $user->twitter = $request['twitter'];
        $user->youtube = $request['youtube'];
        if ($request->file('userImage') != "") {
            $imageName = time() . "." . $request->file('userImage')->extension();
            $request->file('userImage')->move(public_path('Backend/users_images'), $imageName);
            $user->image = $imageName;
            $user->avatar = $imageName;
        }
        $user->update();
        if ($user) {
            return redirect()->back()->with('success', 'Profile Updated Successfully');
        } else {
            return redirect()->back()->with('error', 'Something Went Wrong');
        }
    }


    public function orgProfile()
    {
        $user_id = session()->get('user_id');
        $users = User::find($user_id);
        $events = Events::where('event_author_id', '=', $users->id)->get();
        $totalEvents = $events->count();
        $followers = Followers::where('organizer_id', '=', $user_id)->count();
        $whereIn = $events->pluck('event_id');
        $totalReviews = 0;
        foreach ($whereIn as $column => $values) {
            $totalReviews += eventReviews::where('event_id', '=', $values)->count();
        }
        $data = compact('users', 'events', 'totalEvents', 'followers', 'totalReviews');
        return view('orgProfile')->with($data);
    }

    public function allOrganizers()
    {
        $organizers = User::orderBy('id', 'desc')->where('user_type', '=', 'OA')->get();
        $data = compact('organizers');
        return view('organizers')->with($data);
    }

    public function viewOrgProfile($username)
    {
        $users = Users::where('username', '=', $username)->first();
        $user_id = $users->id;
        $events = Events::orderBy('event_id', 'desc')->where('event_author_id', '=', $users->id)->get();
        $totalEvents = $events->count();
        $followers = Followers::where('organizer_id', '=', $user_id)->count();
        $whereIn = $events->pluck('event_id');
        $totalReviews = 0;
        foreach ($whereIn as $column => $values) {
            $totalReviews += eventReviews::where('event_id', '=', $values)->count();
        }
        $data = compact('users', 'events', 'totalEvents', 'followers', 'totalReviews');
        return view('orgProfile')->with($data);
    }



    public function changePassword(Request $request)
    {
        $request->validate([
            'oldPassword' => 'required',
            'password' => 'required | confirmed',
            'password_confirmation' => 'required',
        ]);

        $oldPassword = Custom::oldPassword($request['oldPassword']);
        if ($oldPassword === true) {
            $user_id = session()->get('user_id');
            $user = Users::find($user_id);
            $user->password = md5($request['password']);
            $user->update();

            return redirect('logout')->with('success', 'Password Updated Successfully. Login With New Password');
        } else {
            return redirect()->back()->with('error', $oldPassword);
        }
    }


    public function edit_orgProfile($id)
    {
        $user = User::find($id);
        $data = compact('user');
        return view('edit_orgProfile')->with($data);
    }

    public function update_orgProfile($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'orgName' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'contact' => 'required',
        ]);

        $user = User::find($id);
        $user->name = $request['name'];
        $user->org_name = $request['orgName'];
        $user->email = $request['email'];
        $user->address = $request['address'];
        $user->contact = $request['contact'];
        $user->website = $request['website'];
        $user->facebook = $request['facebook'];
        $user->instagram = $request['instagram'];
        $user->twitter = $request['twitter'];
        $user->youtube = $request['youtube'];
        if ($request->file('image') != "") {
            $imageName = time() . "-" . rand(1, 100) . "." . $request->file('image')->extension();
            $request->file('image')->move(public_path('Backend/users_images'), $imageName);
            $user->image = $imageName;
            $user->avatar = $imageName;
        }
        if ($request->file('profileBg') != "") {
            $profileBgName = time() . "-" . rand(1, 1000) . "." . $request->file('profileBg')->extension();
            $request->file('profileBg')->move(public_path('Backend/users_images'), $profileBgName);
            $user->profile_bg = $profileBgName;
        }
        // check($user->toArray());
        $user->update();
        if ($user) {
            return redirect()->route('orgProfile');
        }
    }
}
