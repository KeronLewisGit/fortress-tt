<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\Service;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home', [
            'services' => Service::active()->get(),
            'jobs' => JobPosting::live()->orderByDesc('is_featured')->latest('published_at')->take(3)->get(),
        ]);
    }

    public function about()
    {
        return view('pages.about');
    }

    public function services()
    {
        return view('pages.services', ['services' => Service::active()->get()]);
    }

    public function service(Service $service)
    {
        abort_unless($service->is_active, 404);

        return view('pages.service', [
            'service' => $service,
            'others' => Service::active()->where('id', '!=', $service->id)->take(4)->get(),
        ]);
    }

    public function training()
    {
        return view('pages.training', ['jobs' => JobPosting::live()->latest('published_at')->take(3)->get()]);
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function track()
    {
        return redirect()->away(setting('business.tracking_url', fortress('tracking_url')));
    }

    public function sitemap(Request $request)
    {
        $urls = [route('home'), route('about'), route('services'), route('training'), route('careers'), route('contact'), route('privacy')];
        foreach (Service::active()->get() as $s) {
            $urls[] = route('services.show', $s);
        }
        foreach (JobPosting::live()->get() as $j) {
            $urls[] = route('careers.show', $j);
        }

        return response()->view('pages.sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }
}
