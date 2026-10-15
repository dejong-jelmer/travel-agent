<?php

namespace App\Travel\Http\Controllers;

use App\Travel\Http\PageProps;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The library the travel plans pick from. Each section has its own url, so a save returns to the section it was
 * made in.
 */
class LibraryController
{
    /**
     * @param  value-of<PageProps::LIBRARY_SECTIONS>  $section
     */
    public function show(string $section = 'cities'): Response
    {
        return Inertia::render('Admin/Travel/Library', [
            'title' => 'Reisbibliotheek | '.config('app.name'),
            'section' => $section,
            ...PageProps::forLibrary($section),
        ]);
    }
}
