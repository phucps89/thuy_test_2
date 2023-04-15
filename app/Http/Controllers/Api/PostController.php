<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{
    protected function checkInstructor()
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user->is_instructor) {
            throw new AccessDeniedHttpException();
        }
    }

    protected function checkUser()
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user->is_user) {
            throw new AccessDeniedHttpException();
        }
    }

    public function store(Request $request): Response
    {
        $this->checkInstructor();
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'content' => 'required|string|max:65000',
            'id_course' => 'required|int|exists:courses,id',
        ]);

        /** @var User $user */
        $user = auth()->user();

        /** @var Post $post */
        $post = $user->posts()->create($data);

        return \response($post->toArray());
    }

    public function list(): Response
    {
        $this->checkInstructor();
        /** @var User $user */
        $user = auth()->user();
        $posts = $user->posts()->cursorPaginate();

        return $this->responseCursorPagination($posts);
    }

    public function listByUser(Course $course): Response
    {
        $this->checkUser();
        /** @var User $user */
        $user = auth()->user();
        $userCanAccessCourse = $user->courses()->whereKey($course)->exists();
        if (!$userCanAccessCourse) {
            throw new AccessDeniedHttpException();
        }

        $posts = $course->posts()->cursorPaginate();
        return $this->responseCursorPagination($posts);
    }

    public function edit(Post $post, Request $request): Response
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'content' => 'required|string|max:65000',
        ]);

        /** @var User $user */
        $user = auth()->user();
        if ($post->id_user !== $user->getKey()) {
            throw new NotFoundHttpException();
        }

        $post->update($data);

        return \response($post->toArray());
    }

    public function delete(Post $post): Response
    {
        /** @var User $user */
        $user = auth()->user();
        if ($post->id_user !== $user->getKey() && !$user->is_admin) {
            throw new NotFoundHttpException();
        }

        $post->delete();

        return \response(null);
    }

    public function storeComment(Post $post, Request $request): Response
    {
        $this->checkUser();
        /** @var User $user */
        $user = auth()->user();
        $userCanAccessCourse = $user->courses()->whereKey($post->id_course)->exists();
        if (!$userCanAccessCourse) {
            throw new AccessDeniedHttpException();
        }

        $data = $request->validate([
            'comment' => 'required|string|max:65000',
            'id_parent' => 'nullable|int',
        ]);

        $parent = null;
        if (!empty($data['id_parent'])) {
            /** @var Comment $parent */
            $parent = $post->comments()->whereNull('id_parent')->whereKey($data['id_parent'])->first();
            if (empty($parent)) {
                throw new BadRequestHttpException('invalid parent comment');
            }
        }

        $comment = $post->comments()->create([
            'id_user' => $user->getKey(),
            'comment' => $data['comment'],
            'id_parent' => $parent?->getKey(),
        ]);

        return \response($comment->toArray());
    }

    public function deleteComment(Post $post, Comment $comment): Response
    {
        if ($comment->id_post != $post->getKey()) {
            throw new NotFoundHttpException();
        }
        /** @var User $user */
        $user = auth()->user();
        $userCanAccess = false;
        if ($user->is_instructor) {
            $userCanAccess = true;
        } else if ($user->is_user) {
            $userCanAccess = $user->courses()->whereKey($post->id_course)->exists() && $comment->id_user == $user->getKey();
        }

        if (!$userCanAccess) {
            throw new AccessDeniedHttpException();
        }

        $comment->delete();

        return \response(null);
    }
}
