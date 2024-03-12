<?php

namespace App\Http\Controllers;

use App\Models\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\EventType;
use App\Models\GuestCapacity;
use App\Custom;
use App\Models\BuyTickets;
use App\Models\EventImages;
use App\Models\Events;
use App\Models\Followers;
use App\Models\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Console\EventListCommand;
use Illuminate\Support\Facades\Session;

class EventController extends Controller
{
    public function index()
    {
        $eventList = Events::orderBy('event_id', 'desc')->paginate(5);
        $paidEvents = Events::where('event_subscription', '=', 'P')->orderBy('event_id', 'desc')->paginate(5);
        $freeEvents = Events::where('event_subscription', '=', 'F')->orderBy('event_id', 'desc')->paginate(5);
        $eventImages = EventImages::all();
        // $guestCapacity = GuestCapacity::all();

        $data = compact('eventList', 'paidEvents', 'freeEvents', 'eventImages');
        return view('admin.events')->with($data);
    }

    public function approveEvent(Request $request, $eventId)
    {
        $event = Events::findOrFail($eventId);
        $event->approved = !$event->approved; // Toggle the approval status
        $event->save();

        return redirect()->route('admin.events')->with('success', 'Event approval status updated successfully.');
    }
    public function eventTypes()
    {
        $eventType = EventType::all();
        $data = compact('eventType');
        // dd();
        return view('admin.eventTypes', compact('eventType'))->with($data);
    }

    public function createEventType()
    {
        return view('admin.createEventType');
    }

    public function eventTypes_register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eventTypeName' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        } else {
            // Check if the event type already exists
            $eventTypeExists = EventType::where('event_type_name', $request['eventTypeName'])->exists();

            if ($eventTypeExists) {
                // If the event type already exists, set a message in the session
                Session::flash('error', 'Categorie already exists.');
            } else {
                // If the event type does not exist, create a new one
                $eventType = new EventType;
                $eventType->event_type_name = $request['eventTypeName'];
                $eventType->save();
                Session::flash('success', 'Categorie has been Added Successfully.');
            }

            return redirect()->route('admin.eventTypes');
        }
    }


    public function admin_eventType_edit(Request $request)
    {
        $eventType_id = $request->eventType_id;
        $eventType = EventType::find($eventType_id);
        return response()->json([
            'eventType' => $eventType,
        ]);
    }

    public function admin_eventType_update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'edit_eventTypeName' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        } else {
            $eventType = EventType::find($id);
            $eventType->event_type_name = $request['edit_eventTypeName'];
            $eventType->update();
            return response()->json(['success' => 'Updated Successfully']);
        }
    }


    // public function admin_editEventType($id)
    // {
    //     $eventType = EventType::find($id);
    //     return view('admin.editEventType', compact('eventType'));
    // }

    public function editEventType($id)
    {
        $eventType = EventType::findOrFail($id);
        return view('admin.editEventType', compact('eventType'));
    }


    public function updateEventType(EventType $eventType)
    {
        $validated = request()->validate([
            'edit_eventTypeName' => 'required'
        ]);

        $eventType->update([
            'event_type_name' => $validated['edit_eventTypeName']
        ]);

        return redirect()->route('admin.eventTypes')->with('success', 'Categorie Updated Successfully.');
    }




    // public function admin_editEventType($id)
    // {
    //     $eventType = EventType::findOrFail($id);
    //     return view('admin.editEventType', compact('eventType'));
    //     return view('admin.editEventType', compact('eventType'));
    // }

    // public function admin_editEventType($id)
    // {
    //     $eventType = EventType::findOrFail($id);
    //     return view('admin.editEventType', compact('eventType'));
    // }

    // public function editEventType($id)
    // {
    //     $eventType = EventType::findOrFail($id);
    //     return view('admin.edit', compact('eventType'));
    // }


    public function admin_eventType_delete($id)
    {
        // Find the event type by ID
        $eventType = EventType::find($id);

        // Check if the event type exists
        if ($eventType) {
            // Delete all events associated with the event type
            $eventsToDelete = Events::where('event_type_id', $id)->get();
            foreach ($eventsToDelete as $event) {
                $event->delete();
            }

            // Delete the event type itself
            $eventType->delete();

            return redirect()->route('admin.eventTypes')->with('success', 'Categorie and associated events have been deleted successfully.');
        } else {
            return redirect()->route('admin.eventTypes')->with('error', 'Categorie not found.');
        }
    }


    public function admin_createEvent()
    {
        // Fetch event types with their events
        $eventTypes = EventType::with('events')->get();

        // Fetch other necessary data
        $cities = Cities::all();
        $images = EventImages::all();

        // Pass data to the view
        $data = compact('cities', 'eventTypes');
        return view('admin.createEvent', $data);
    }




    public function admin_storeEvent(Request $request)
    {
        $request->validate([
            'eventName' => 'required',
            'eventLocation' => 'required',
            'eventAddress' => 'required',
            'startDate' => 'required',
            'startTime' => 'required',
            'endDate' => 'required',
            'endTime' => 'required',
            'guestCapacity' => 'required',
            'eventSubscription' => 'required',
            'eventDescription' => 'required',
            'eventType' => 'required', // Add validation for event type selection
            'files' => 'required',
        ]);

        $event_author_id = session()->has('event_author_id') ? session()->get('event_author_id') : null;
        $eventLocation = Custom::cityName($request['eventLocation']);
        $slug = Custom::slug($request['eventName'] . " in " . $eventLocation . " " . time());

        // Create a new event instance
        $event = new Events;

        // Assign values from the request to the event object
        $event->event_name = $request['eventName'];
        $event->event_location = $request['eventLocation'];
        $event->event_address = $request['eventAddress'];
        $event->event_start_date = $request['startDate'];
        $event->event_start_time = $request['startTime'];
        $event->event_end_date = $request['endDate'];
        $event->event_end_time = $request['endTime'];
        $event->event_guestCapacity = $request['guestCapacity'];
        $event->event_subscription = $request['eventSubscription'];
        $event->event_type_id = $request['eventType']; // Store the selected event type ID
        $event->event_description = $request['eventDescription'];
        $event->event_author_id = $event_author_id; // Use the default value
        $event->event_slug = $slug;
        $event->save();
        $event_newId = $event->event_id;

        // Image Uploading to the Other Table
        if ($request['files'] != "") {
            $i = 1;
            foreach ($request->file('files') as $uploadImages) {
                $eventImages = new EventImages;

                $imageName = $event_newId . '_' . $i . '_' . time() . "." . $uploadImages->extension();
                $uploadImages->move(public_path('Backend/event_images'), $imageName);
                $eventImages->event_image_path = $imageName;
                $eventImages->event_list_id = $event_newId;
                $eventImages->save();
                $i++;
            }
        }

        // Notify followers about the new event
        $followers = Followers::where('organizer_id', '=', session()->get('user_id'))->get();
        $users = $followers->pluck('user_id');
        foreach ($users as $key => $value) {
            $notification = new Notification;
            $notification->noti_title = 'Organizer: ' . session()->get('org_name') . ' created new Event';
            $notification->noti_for = 'U';
            $notification->noti_forId = $value;
            $notification->noti_type = 'E';
            $notification->noti_typeId = $event_newId;
            $notification->noti_byId = session()->get('user_id');
            $notification->save();
        }

        // Redirect the user to the appropriate route based on user type
        if (session()->has('user_type') && session()->get('user_type') == 'A') {
            // For admin users
            $event->event_author_id = session()->get('admin_id');
            return redirect()->route('admin.events')->with('success', 'Successfully Added');
        } else {
            return redirect()->route('org_events')->with('success', 'Successfully Added');
        }
    }


    public function organizer_storeEvent(Request $request)
    {
        $request->validate([
            'eventName' => 'required',
            'eventLocation' => 'required',
            'eventAddress' => 'required',
            'startDate' => 'required',
            'startTime' => 'required',
            'endDate' => 'required',
            'endTime' => 'required',
            'guestCapacity' => 'required',
            'eventSubscription' => 'required',
            'eventDescription' => 'required',
            'eventType' => 'required', // Add validation for event type selection
            'files' => 'required',
            'reservationMethod' => 'required', // Validate reservation method
        ]);

        $eventLocation = Custom::cityName($request['eventLocation']);
        $slug = Custom::slug($request['eventName'] . " in " . $eventLocation . " " . time());

        // Create a new event instance
        $event = new Events;

        // Assign values from the request to the event object
        $event->event_name = $request['eventName'];
        $event->event_location = $request['eventLocation'];
        $event->event_address = $request['eventAddress'];
        $event->event_start_date = $request['startDate'];
        $event->event_start_time = $request['startTime'];
        $event->event_end_date = $request['endDate'];
        $event->event_end_time = $request['endTime'];
        $event->event_guestCapacity = $request['guestCapacity'];
        $event->event_subscription = $request['eventSubscription'];
        if ($request['eventTicketPrice'] == "") {
            $event->event_ticket_price = '0';
        } else {
            $event->event_ticket_price = $request['eventTicketPrice'];
        }
        $event->event_type_id = $request['eventType']; // Store the selected event type ID
        $event->event_description = $request['eventDescription'];
        $event->event_author_id = session()->get('user_id');
        $event->event_reservation_method = $request['reservationMethod'];
        $event->event_slug = $slug;
        $event->save();
        $event_newId = $event->event_id;

        // Image Uploading to the Other Table
        if ($request['files'] != "") {
            $i = 1;
            foreach ($request->file('files') as $uploadImages) {
                $eventImages = new EventImages;

                $imageName = $event_newId . '_' . $i . '_' . time() . "." . $uploadImages->extension();
                $uploadImages->move(public_path('Backend/event_images'), $imageName);
                $eventImages->event_image_path = $imageName;
                $eventImages->event_list_id = $event_newId;
                $eventImages->save();
                $i++;
            }
        }

        // Notify followers about the new event
        $followers = Followers::where('organizer_id', '=', session()->get('user_id'))->get();
        $users = $followers->pluck('user_id');
        foreach ($users as $key => $value) {
            $notification = new Notification;
            $notification->noti_title = 'Organizer: ' . session()->get('org_name') . ' created new Event';
            $notification->noti_for = 'U';
            $notification->noti_forId = $value;
            $notification->noti_type = 'E';
            $notification->noti_typeId = $event_newId;
            $notification->noti_byId = session()->get('user_id');
            $notification->save();
        }

        // Redirect the user to the appropriate route based on user type
        if (session()->has('user_type') && session()->get('user_type') == 'A') {
            // For admin users
            $event->event_author_id = session()->get('admin_id');
            return redirect()->route('admin.events')->with('success', 'Successfully Added');
        } else {
            return redirect()->route('org_events')->with('success', 'Successfully Added');
        }
    }




    public function admin_editEvent($id)
    {
        $event = Events::find($id);
        $cities = Cities::all();
        $eventTypes = EventType::all(); // Fetch event types
        $images = EventImages::where('event_list_id', '=', $id)->get();

        return view('admin.editEvent')->with(compact('event', 'cities', 'eventTypes', 'images'));
    }


    public function admin_editImage($id)
    {
        $images = EventImages::find($id);
        $img_path = public_path("Backend/event_images/" . $images->event_image_path);
        File::delete($img_path);
        $images->delete();
        return response()->json(['success' => 'Image has been Deleted Successfully']);
    }

    public function admin_updateEvent($id, Request $request)
    {
        $request->validate([
            'eventName' => 'required',
            'eventLocation' => 'required',
            'eventAddress' => 'required',
            'startDate' => 'required',
            'startTime' => 'required',
            'endDate' => 'required',
            'endTime' => 'required',
            'guestCapacity' => 'required',
            'eventSubscription' => 'required',
            'eventDescription' => 'required',
            'eventType' => 'required', // Add validation for event type selection
            // 'files' => 'required',
        ]);

        $eventLocation = Custom::cityName($request['eventLocation']);
        $slug = Custom::slug($request['eventName'] . " in " . $eventLocation . " " . time());

        // Find the event by ID
        $event = Events::find($id);

        // Update event details
        $event->event_name = $request['eventName'];
        $event->event_location = $request['eventLocation'];
        $event->event_address = $request['eventAddress'];
        $event->event_start_date = $request['startDate'];
        $event->event_start_time = $request['startTime'];
        $event->event_end_date = $request['endDate'];
        $event->event_end_time = $request['endTime'];
        $event->event_guestCapacity = $request['guestCapacity'];
        $event->event_subscription = $request['eventSubscription'];
        $event->event_type_id = $request['eventType']; // Update event type
        $event->event_ticket_price = $request['eventTicketPrice'] ?? '0'; // Set default value if null
        $event->event_description = $request['eventDescription'];
        $event->event_author_id = session()->get('event_author_id'); // Update event author ID
        $event->event_slug = $slug;
        $event->save(); // Save changes to the event

        // Upload new images if provided
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $uploadImages) {
                $imageName = $event->event_id . '_' . time() . "." . $uploadImages->extension();
                $uploadImages->move(public_path('Backend/event_images'), $imageName);

                // Save image details to the EventImages model
                $eventImage = new EventImages;
                $eventImage->event_image_path = $imageName;
                $eventImage->event_list_id = $event->event_id;
                $eventImage->save();
            }
        }

        // Redirect to appropriate route based on user type
        if (session()->get('user_type') == 'A') {
            return redirect()->route('admin.events')->with('success', 'Successfully Updated');
        } else {
            return redirect()->route('org_events')->with('success', 'Successfully Updated');
        }
    }

    public function org_updateEvent($id, Request $request)
    {
        $request->validate([
            'eventName' => 'required',
            'eventLocation' => 'required',
            'eventAddress' => 'required',
            'startDate' => 'required',
            'startTime' => 'required',
            'endDate' => 'required',
            'endTime' => 'required',
            'guestCapacity' => 'required',
            'eventSubscription' => 'required',
            'eventDescription' => 'required',
            'eventType' => 'required', // Add validation for event type selection
            // 'files' => 'required',
        ]);

        $eventLocation = Custom::cityName($request['eventLocation']);
        $slug = Custom::slug($request['eventName'] . " in " . $eventLocation . " " . time());

        // Find the event by ID
        $event = Events::find($id);

        // Update event details
        $event->event_name = $request['eventName'];
        $event->event_location = $request['eventLocation'];
        $event->event_address = $request['eventAddress'];
        $event->event_start_date = $request['startDate'];
        $event->event_start_time = $request['startTime'];
        $event->event_end_date = $request['endDate'];
        $event->event_end_time = $request['endTime'];
        $event->event_guestCapacity = $request['guestCapacity'];
        $event->event_subscription = $request['eventSubscription'];
        $event->event_type_id = $request['eventType']; // Update event type
        $event->event_ticket_price = $request['eventTicketPrice'] ?? '0'; // Set default value if null
        $event->event_description = $request['eventDescription'];
        $event->event_author_id = session()->get('user_id'); // Update event author ID
        $event->event_slug = $slug;
        $event->save(); // Save changes to the event

        // Upload new images if provided
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $uploadImages) {
                $imageName = $event->event_id . '_' . time() . "." . $uploadImages->extension();
                $uploadImages->move(public_path('Backend/event_images'), $imageName);

                // Save image details to the EventImages model
                $eventImage = new EventImages;
                $eventImage->event_image_path = $imageName;
                $eventImage->event_list_id = $event->event_id;
                $eventImage->save();
            }
        }

        // Redirect to appropriate route based on user type
        if (session()->get('user_type') == 'A') {
            return redirect()->route('admin.events')->with('success', 'Successfully Updated');
        } else {
            return redirect()->route('org_events')->with('success', 'Successfully Updated');
        }
    }



    public function editEvent($id)
    {

        $eventTypes = EventType::all();
        $event = Events::find($id);
        $cities = Cities::all();
        $images = EventImages::where('event_list_id', '=', $id)->get();
        $data = compact('event', 'cities', 'images', 'eventTypes');
        return view('edit_event')->with($data);
    }


    public function deleteEvent($id)
    {
        $event = Events::find($id);
        if (!$event) {
            // Event not found
            return redirect()->back()->with('error', 'Event not found.');
        }

        // Delete the event
        $eventImages = EventImages::where('event_list_id', $id)->get();
        $eventTickets = BuyTickets::where('buyer_event_id', $id)->get();
        $event->delete();

        // Delete related images
        foreach ($eventImages as $img) {
            $img_path = public_path("Backend/event_images/" . $img->event_image_path);
            File::delete($img_path);
            $img->delete();
        }

        // Delete related tickets
        foreach ($eventTickets as $ticket) {
            $ticket->delete();
        }

        // Redirect with success message
        return redirect()->route('admin.events')->with('success', 'Event successfully deleted.');
    }


    // Organizer delete their event
    public function deleteMyEvent($id)
    {
        $events = Events::find($id);
        $eventImages = EventImages::where('event_list_id', '=', $events->event_id)->get();
        $eventTickets = BuyTickets::where('buyer_event_id', '=', $events->event_id)->get();
        $events->delete();

        foreach ($eventImages as $img) {
            $img_path = public_path("Backend/event_images/" . $img->event_image_path);
            File::delete($img_path);
            $img->delete();
        }
        foreach ($eventTickets as $ticket) {
            $ticket->delete();
        }
        if (session()->get('user_type') == 'A') {
            return view('admin.events');
        } else {
            return view('myEvents');
        }
    }

    // public function admin_createEvent()
    // {
    //     // Fetch event types with their events
    //     $eventTypes = EventType::with('events')->get();

    //     // Fetch other necessary data
    //     $cities = Cities::all();
    //     $images = EventImages::all();

    //     // Pass data to the view
    //     $data = compact('cities', 'eventTypes');
    //     return view('admin.createEvent', $data);
    // }
    public function create_event()
    {
        $eventTypes = EventType::with('events')->get();

        $guestCapacity = GuestCapacity::all();
        $cities = Cities::all();
        $images = EventImages::all();
        $data = compact('guestCapacity', 'cities', 'eventTypes');
        return view('createEvent')->with($data);
    }

    public function org_events()
    {
        $user_id = session()->get('user_id');
        $eventList = Events::orderBy('event_id', 'desc')->where('event_author_id', '=', $user_id)->get();
        $totalEvents = $eventList->count();
        // $activeEvents = $eventList->where('event_status', '=', 1)->count();
        // $inActiveEvents = $eventList->where('event_status', '=', 0)->count();
        $date = date('Y-m-d');
        $completedEvents = $eventList->where('event_start_date', '<', $date);
        $completedTotalEvents = $eventList->where('event_start_date', '<', $date)->count();
        $upcommingEvents = $eventList->where('event_start_date', '>', $date);
        $upcommingTotalEvents = $eventList->where('event_start_date', '>', $date)->count();
        $TodayEvents = $eventList->where('event_start_date', '=', $date);
        $TodayTotalEvents = $eventList->where('event_start_date', '=', $date)->count();
        // echo '<pre>';
        // print_r($completedEvents->toArray());
        // die;
        $data = compact('totalEvents', 'upcommingEvents', 'upcommingTotalEvents', 'completedEvents', 'completedTotalEvents', 'TodayEvents', 'TodayTotalEvents');
        return view('myEvents')->with($data);
    }

    public function user_events()
    {
        $user_id = session()->get('user_id');
        $bookEvents = BuyTickets::orderBy('buy_ticket_id', 'desc')->where('buyer_user_id', '=', $user_id)->get();
        $eventId = $bookEvents->pluck('buyer_event_id');
        // $events = Events::where('event_id', '=', $eventId);

        $events = Events::orderBy('event_id', 'desc')->get();
        $whereIn = ['event_id' => $eventId];
        foreach ($whereIn as $column => $values) {
            $events = $events->whereIn($column, $values);
        }

        $data = compact('events');
        return view('userBookEvents')->with($data);
    }
}
