<?php

namespace App\Http\Controllers;

use App\Models\BuyTickets;
use App\Models\EventImages;
use App\Models\Events;
use Illuminate\Http\Request;
use App\Custom;
use App\Mail\TicketValidated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\HomeController;
use Dompdf\Dompdf;


class TicketController extends Controller
{
    public function index()
    {
        $tickets = BuyTickets::orderBy('buy_ticket_id', 'desc')->get();
        $data = compact('tickets');
        return view('admin.tickets')->with($data);
    }
    public function viewTicketDetails($id)
    {
        $event = Events::find($id);
        $eventImages = EventImages::where('event_list_id', '=', $id)->get();
        $eventName = Custom::orgName($event->event_author_id);
        if ($event->event_subscription == 'P') {
            $eventSubscription = '<span class="badge badge-danger">Paid</span>';
            $eventTicketPrice = '<span>' . $event->event_ticket_price . '</span>';
        } elseif ($event->event_subscription == 'F') {
            $eventSubscription = '<span class="badge badge-danger">Free</span>';
            $eventTicketPrice = '<span>0</span>';
        }
        // else{
        //     $eventSubscription = $event->event_subscription;
        // }
        $totalTickets = $event->event_guestCapacity;
        $remainingTickets = Custom::availableSeats($event->event_id);
        $eventLocation = Custom::cityName($event->event_location);
        $eventAddress = $event->event_address;
        $eventStartDate = "(<i class='fa fas fa-calendar-alt text-primary'></i> " . $event->event_start_date . ") " . " " . " (<i class='fa far fa-clock text-primary'></i> " . $event->event_start_time . ")";
        $eventEndDate = "(<i class='fa fas fa-calendar-alt text-primary'></i> " . $event->event_end_date . ") " . " " . " (<i class='fa far fa-clock text-primary'></i> " . $event->event_end_time . ")";
        if ($event) {
            return response()->json([
                'event' => $event,
                'eventImages' => $eventImages,
                'eventName' => $eventName,
                'eventSubscription' => $eventSubscription,
                'eventTicketPrice' => $eventTicketPrice,
                'totalTickets' => $totalTickets,
                'remainingTickets' => $remainingTickets,
                'eventLocation' => $eventLocation,
                'eventAddress' => $eventAddress,
                'eventStartDate' => $eventStartDate,
                'eventEndDate' => $eventEndDate,
            ]);
        }
    }



    public function updateStatus($id, Request $request)
    {
        $homeController = new HomeController();
        $ticket = BuyTickets::findOrFail($id);
        $event = Events::find($ticket->buyer_event_id);
        $qrCode = $homeController->generateQRCode($ticket);

        // Check if the event is found
        if (!$event) {
            return redirect()->back()->with('error', 'Event not found.');
        }

        // Check if the logged-in user is authorized to update the ticket status
        $authorizedUser = DB::table('users')
            ->select('id')
            ->where('id', $request->user()->id)
            ->where('name', $request->user()->name)
            ->where('user_type', '=', 'OA')
            ->orderBy('id', 'desc')
            ->first();

        $authorizedOrganizer = DB::table('organizers')
            ->select('id')
            ->where('id', $request->user()->id)
            ->where('name', $request->user()->name)
            ->where('user_type', '=', 'OA')
            ->orderBy('id', 'desc')
            ->first();

        $authorizedAdmin = DB::table('admins')
            ->select('id')
            ->where('id', $request->user()->id)
            ->where('name', $request->user()->name)
            ->where('user_type', '=', 'OA')
            ->orderBy('id', 'desc')
            ->first();
        // If the user is authorized, update the ticket status
        if ($authorizedUser || $authorizedOrganizer || $authorizedAdmin) {
            $ticket->validation = '1';
            $ticket->save();

            Mail::to($ticket->buyer_user_email)->send(new TicketValidated($ticket, $qrCode, $event));

            return redirect()->back()->with('success', 'Ticket status updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
    }


    public function org_tickets()
    {
        if (session()->get('user_type') == 'OA') {
            $user_id = session()->get('user_id');
            $events = Events::where('event_author_id', '=', $user_id)->get();
            $eventId = $events->pluck('event_author_id')->first();
            $soldTickets = BuyTickets::orderBy('buy_ticket_id', 'desc')->where('buyer_event_author_id', '=', $eventId)->paginate(10);
            $data = compact('soldTickets');
            return view('myTickets')->with($data);
        } elseif (session()->get('user_type') == 'U') {
            $user_id = session()->get('user_id');
            // $events = Events::where('event_author_id', '=', $user_id)->get();
            // $eventId = $events->pluck('event_author_id')->first();
            $soldTickets = BuyTickets::orderBy('buy_ticket_id', 'desc')->where('buyer_user_id', '=', $user_id)->paginate(10);
            $data = compact('soldTickets');
            return view('myTickets')->with($data);
            // return redirect('events');
        }
    }

    public function org_ticket_details($slug)
    {
        $eventId = Events::where('event_slug', '=', $slug)->first();
        $soldTickets = BuyTickets::where('buyer_event_id', '=', $eventId->event_id)->orderBy('buy_ticket_id', 'desc')->get();
        //  echo '<pre>';
        //  print_r($tickets->toArray());
        //  die;
        $data = compact('soldTickets');
        return view('myTickets')->with($data);
    }


    public function payment()
    {
        return view('payment');
    }
}
