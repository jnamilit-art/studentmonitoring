<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Announcement;
use App\Services\ParentNotifier;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'date' => now(),
        ]);

        app(ParentNotifier::class)->announcementPosted($announcement);

        return redirect()->route('teacher.announcements')->with('success', 'Announcement added successfully. Parent emails sent.');
    }

    public function edit(Announcement $announcement): Response
    {
        return Inertia::render('Admin/AnnouncementsEdit', [
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
            ],
        ]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        app(ParentNotifier::class)->announcementUpdated($announcement);

        return redirect()->route('teacher.announcements')->with('success', 'Announcement updated successfully. Parent emails sent.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('teacher.announcements')->with('success', 'Announcement deleted successfully.');
    }
}
