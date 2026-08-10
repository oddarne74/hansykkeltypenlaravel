<?php

namespace WebStats\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use WebStats\Models\WebStat;
use WebStats\Models\WebStatHit;

class TrackWebStats
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            try {
                $url = $request->fullUrl();
                $title = $this->extractTitle($response) ?? $request->path();

                $webStat = WebStat::firstOrCreate(
                    ['url' => $url],
                    ['title' => $title]
                );

                if (empty($webStat->title) && $title) {
                    $webStat->update(['title' => $title]);
                }

                WebStatHit::create([
                    'web_stat_id' => $webStat->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {
                // Fail silently if stats table/database is not yet migrated
            }
        }

        return $response;
    }

    protected function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return false;
        }

        $path = $request->path();
        if (str_starts_with($path, 'admin') || str_starts_with($path, 'livewire') || str_starts_with($path, '_')) {
            return false;
        }

        $status = $response->getStatusCode();
        if ($status >= 400) {
            return false;
        }

        return true;
    }

    protected function extractTitle(Response $response): ?string
    {
        $content = $response->getContent();
        if (is_string($content) && preg_match('/<title[^>]*>(.*?)<\/title>/si', $content, $matches)) {
            return trim(html_entity_decode(strip_tags($matches[1])));
        }

        return null;
    }
}
