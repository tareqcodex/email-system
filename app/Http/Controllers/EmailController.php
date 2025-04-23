<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\SentEmail;
use App\Jobs\SendEmailJob;
use App\Models\EmailList;

class EmailController extends Controller
{
    public function form()
    {
        $emails = EmailList::pluck('email', 'id'); // id => email
        return view('email.create', compact('emails'));
    }


    public function list(Request $request)
    {
        $query = SentEmail::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('to', 'like', "%$search%")
                  ->orWhere('subject', 'like', "%$search%");
        }

        $emails = $query->latest()->paginate(10);

        return view('email.list', compact('emails'));
    }

    public function send(Request $request)
{
    try {
        $request->validate([
            'to' => 'required',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:20480',
        ]);
        

        $emails = array_map('trim', explode(',', $request->to));

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('attachments', $filename, 'private');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                ];
            }
        }

        dispatch(new SendEmailJob([
            'to' => implode(',', $emails),
            'subject' => $request->subject,
            'description' => $request->description ?? '',
            'attachments' => $attachments,
        ]));

        SentEmail::create([
            'to' => implode(', ', $emails),
            'subject' => $request->subject,
            'description' => $request->description ?? '',
        ]);

        return response()->json(['message' => 'Email is queued successfully!'], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Email send error: ' . $e->getMessage());
        return response()->json(['message' => 'Something went wrong!'], 500);
    }
}

 

    public function bulkDelete(Request $request){
        $ids = $request->input('ids');

        if ($ids && count($ids) > 0) {
            SentEmail::whereIn('id', $ids)->delete();
            return redirect()->route('sent.email.list')->with('success', 'Selected emails deleted successfully!');
        }        
    }
}
