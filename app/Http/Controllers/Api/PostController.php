<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\PostApiResource;

class PostController extends Controller
{
    // public function index(Request $request)
    // {
    //     $perPage          = min(max((int) $request->query('per_page', 10), 1), 100);
    //     $include          = collect(explode(',', (string) $request->query('include', '')))
    //                             ->map(fn($v) => trim($v))
    //                             ->filter()
    //                             ->all();
    //     $withoutPagination = $request->boolean('without_pagination');
    //     $single            = $request->boolean('single');

    //     $q = Post::query();

    //     // Eager loads
    //     if (!empty($include)) {
    //         $allowed = ['author', 'category'];
    //         $q->with(array_intersect($include, $allowed));
    //     }

    //     // Filters
    //     if ($s = trim((string) $request->query('search', ''))) {
    //         $q->where(function ($qq) use ($s) {
    //             $qq->where('title', 'like', "%{$s}%")
    //                ->orWhere('content', 'like', "%{$s}%")
    //                ->orWhere('short_description', 'like', "%{$s}%");
    //         });
    //     }
    //     if ($cid = $request->query('category_id')) {
    //         $q->where('category_id', $cid);
    //     }
    //     if ($aid = $request->query('author_id')) {
    //         $q->where('author_id', $aid);
    //     }
    //     if ($tag = trim((string) $request->query('tag', ''))) {
    //         // Works for JSON array or CSV column
    //         $q->where(function ($qq) use ($tag) {
    //             $qq->whereJsonContains('tags', $tag)
    //                ->orWhere('tags', 'like', "%{$tag}%");
    //         });
    //     }

    //     $q->latest('published_date')->latest('id');

    //     // Single record shape: { "data": { ... } }
    //     if ($single) {
    //         $post = $q->firstOrFail();
    //         return new PostApiResource($post);
    //     }

    //     // No pagination: { "data": [ ... ] }
    //     if ($withoutPagination) {
    //         return PostApiResource::collection($q->get());
    //     }

    //     // Default: paginated
    //     return PostApiResource::collection($q->paginate($perPage));
    // }
    public function index(Request $request)
    {
        // ------- Query params -------
        $perPage            = min(max((int) $request->query('per_page', 10), 1), 100);
        $include            = collect(explode(',', (string) $request->query('include', '')))
                                ->map(fn ($v) => trim($v))->filter()->all();
        $withoutPagination  = $request->boolean('without_pagination');
        $single             = $request->boolean('single');

        // Optional filters (all safe to omit)
        $search     = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category_id');
        $authorId   = $request->query('author_id');
        $tag        = trim((string) $request->query('tag', ''));   // works if tags stored JSON or CSV
        $slug       = trim((string) $request->query('slug', ''));  // optional: fetch by slug via index
        $sort       = (string) $request->query('sort', '-published_date,-id'); // CSV; "-" = desc

        // ------- Base query -------
        $q = Post::query();

        // Eager loads
        if (!empty($include)) {
            $allowed = ['author', 'category'];
            $q->with(array_intersect($include, $allowed));
        }

        // Filters
        if ($search !== '') {
            $q->where(function ($qq) use ($search) {
                $qq->where('title', 'like', "%{$search}%")
                   ->orWhere('content', 'like', "%{$search}%")
                   ->orWhere('short_description', 'like', "%{$search}%");
            });
        }
        if ($categoryId) {
            $q->where('category_id', $categoryId);
        }
        if ($authorId) {
            $q->where('author_id', $authorId);
        }
        if ($tag !== '') {
            $q->where(function ($qq) use ($tag) {
                $qq->whereJsonContains('tags', $tag)
                   ->orWhere('tags', 'like', "%{$tag}%");
            });
        }
        if ($slug !== '') {
            $q->where('slug', $slug);
        }

        // Sort (supports comma-separated: e.g., "-published_date,-id" / "title")
        foreach (array_filter(array_map('trim', explode(',', $sort))) as $field) {
            $direction = str_starts_with($field, '-') ? 'desc' : 'asc';
            $column    = ltrim($field, '-+');
            // whitelist known sortable columns
            if (in_array($column, ['published_date', 'id', 'title'], true)) {
                $q->orderBy($column, $direction);
            }
        }

        // ------- Shapes -------
        // Single record: {"data": {...}}
        if ($single) {
            $post = $q->firstOrFail();
            return new PostApiResource($post);
        }

        // No pagination: {"data": [...]}
        if ($withoutPagination) {
            return PostApiResource::collection($q->get());
        }

        // Default: Paginated but WITHOUT any "links" arrays
        $paginator = $q->paginate($perPage)->appends($request->query());

        // Transform items only (keeps your resource mapping)
        $items = PostApiResource::collection($paginator->getCollection())->resolve();

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from'         => $paginator->firstItem(),
                'last_page'    => $paginator->lastPage(),
                'path'         => $paginator->path(),
                'per_page'     => $paginator->perPage(),
                'to'           => $paginator->lastItem(),
                'total'        => $paginator->total(),
            ],
        ]);
    }
    /**
     * GET /api/posts/{post}
     * Include relations with ?include=author,category
     */
    public function show(Request $request, \App\Models\Post $post)
{
    $include = collect(explode(',', (string) $request->query('include', '')))
        ->map(fn($v) => trim($v))
        ->filter()
        ->all();

    $allowed   = ['author', 'category'];
    $relations = array_intersect($include ?: ['author','category'], $allowed);

    // Load only if missing (prevents double queries)
    $post->loadMissing([
        ...collect($relations)->map(fn($r) => match ($r) {
            'author'   => 'author:id,name,email',
            'category' => 'category:id,name,slug',
        })->all()
    ]);

    return new \App\Http\Resources\PostApiResource($post);
}
}
