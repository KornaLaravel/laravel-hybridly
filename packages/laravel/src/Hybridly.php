<?php

namespace Hybridly;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\Macroable;
use Hybridly\View\Factory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Hybridly
{
    use Macroable;
    use Concerns\HasRootView;
    use Concerns\HasSharedProperties;
    use Concerns\HasPersistentProperties;
    use Concerns\HasVersion;

    public const HYBRIDLY_HEADER = 'x-hybridly';
    public const EXTERNAL_HEADER = 'x-hybridly-external';
    public const PARTIAL_COMPONENT_HEADER = 'x-hybridly-partial-component';
    public const ONLY_DATA_HEADER = 'x-hybridly-only-data';
    public const EXCEPT_DATA_HEADER = 'x-hybridly-except-data';
    public const CONTEXT_HEADER = 'x-hybridly-context';
    public const ERROR_BAG_HEADER = 'x-hybridly-error-bag';
    public const VERSION_HEADER = 'x-hybridly-version';
    public const DEFAULT_ROOT_VIEW = 'root';

    /**
     * Returns a hybridly view.
     */
    public function view(string $component, array|Arrayable $properties = []): Factory
    {
        return resolve(Factory::class)->view($component, $properties);
    }

    /**
     * Redirects to the given URL using a full page load.
     */
    public function external(string|RedirectResponse $url): Response
    {
        if ($url instanceof RedirectResponse) {
            $url = $url->getTargetUrl();
        }

        if ($this->isHybridly()) {
            return new Response(
                status: Response::HTTP_CONFLICT,
                headers: [self::EXTERNAL_HEADER => $url],
            );
        }

        return new RedirectResponse($url);
    }

    /**
     * Checks if the request is hybridly.
     */
    public function isHybridly(Request $request = null): bool
    {
        $request ??= request();

        return $request->headers->has(self::HYBRIDLY_HEADER);
    }
}