<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailList;
use Illuminate\Support\Facades\Validator;

class EmailListController extends Controller
{
    public function index(Request $request){
        $search = $request->get('search');
        $emails = EmailList::when($search, fn($q) =>
            $q->where('email', 'like', "%{$search}%")
        )->orderBy('id', 'desc')->paginate(10);

        return view('email-list.index', compact('emails', 'search'));
    }

    public function upload(Request $request)
    {
          // Validate file type
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Only CSV files are allowed (Max: 10MB).');
        }

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        $saved = 0; $skipped = 0;

        while (($data = fgetcsv($file)) !== false) {
            $email = trim($data[0]);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++; continue;
            }

            if (EmailList::where('email', $email)->exists()) {
                $skipped++; continue;
            }

            EmailList::create(['email' => $email]);
            $saved++;
        }

        fclose($file);

        return redirect()->back()->with('success', "$saved email(s) uploaded successfully. $skipped skipped.");
    }

    public function bulkDelete(Request $request){
        $ids = $request->input('ids');

        if ($ids && count($ids) > 0) {
            EmailList::whereIn('id', $ids)->delete();
            return redirect()->route('email.list')->with('success', 'Selected emails deleted successfully!');
        }        
    }
}
