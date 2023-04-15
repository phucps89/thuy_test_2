<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CourseController extends Controller
{
    public function store(Request $request): Response
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user->is_instructor) {
            throw new AccessDeniedHttpException();
        }
//        return \response(['x' => 1]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);


        $course = Course::query()->create($data);

        return \response($course->toArray());
    }

    public function register(Course $course): Response
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user->is_user) {
            throw new AccessDeniedHttpException();
        }

        $course->registrations()
            ->updateOrCreate([
                'id_user' => $user->getKey(),
            ]);

        return \response(null, Response::HTTP_CREATED);
    }

    public function delete(Course $course): Response
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user->is_admin) {
            throw new AccessDeniedHttpException();
        }

        $course->delete();

        return \response(null);
    }
}
