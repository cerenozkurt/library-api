<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\User\LibraryController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('rytdeneme', [Controller::class, 'rytdeneme']);
Route::get('bildirim',[LikeController::class, 'bildirim']);
Route::post('deneme', [LibraryController::class, 'zamandenemesi']);
//private rules-> logged in users 
Route::prefix('auth')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('user')->group(function () {

            Route::resource('post', PostController::class)->only(['store', 'update', 'destroy']);
            Route::get('/post', [PostController::class, 'myPosts']);
            Route::post('/post/{post}/comment', [CommentController::class, 'postStore'])->middleware('post.user.id.control');
            Route::put('comment/{comment}', [CommentController::class, 'update'])->middleware('comment.id.control');
            Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->middleware('comment.id.control');
            Route::get('/comment/{comment}', [CommentController::class, 'show'])->middleware('comment.id.control');

            Route::controller(AuthController::class)->group(function () {
                Route::get('/', 'index')->middleware('check.roles:1');
                Route::post('/edit', 'editProfile')->middleware('check.roles:1|2|3');
                Route::post('/{user}', 'update')->middleware('check.roles:1');
                Route::post('/{user}/role', 'roleAssignment')->middleware('check.roles:1')->middleware('user.id.control');
                Route::get('/logout', 'logout')->middleware('check.roles:1|2|3');
            });

            Route::controller(FollowController::class)->prefix('follow')->group(function(){
                Route::get('sent', 'followSent');
                Route::get('from','followFrom');
                Route::delete('/{friend}/delete', 'rejectFollow')->middleware('friend.id.control');
                Route::get('/{friend}/accept','acceptFollow')->middleware('friend.id.control');
                Route::get('/friends','getMyFriends');
                Route::get('/{user}/friends','getUsersFriends')->middleware('user.id.control');
                Route::delete('/{friend}', 'deleteFriend')->middleware('friend.id.control');
                Route::post('/friend','addFriend');
                Route::delete('/request/{friend}','deleteRequest')->middleware('friend.id.control');

            });

            Route::controller(LikeController::class)->prefix('like')->group(function(){
                Route::post('/{post}/post', 'addPostLike')->middleware('post.id.control');
                Route::post('/{comment}/quotes', 'addQuotesLike')->middleware('quotes.id.control');
                Route::post('/{comment}/comment', 'addCommentLike')->middleware('comment.id.control');
                Route::delete('/{like}', 'deleteLike')->middleware('like.id.control');


            });
        });



      

        Route::prefix('profile')->controller(ProfileController::class)->group(function () {
            Route::post('/photo', 'uploadProfilePicture')->middleware('check.roles:1|2|3');
            Route::delete('/photo', 'deleteProfilePicture')->middleware('check.roles:1|2|3');
        });

        Route::prefix('library')->middleware('check.roles:1|2|3')->group(function () {
            Route::get('/', [LibraryController::class, 'getMyLibrary']);
            Route::get('/{status}', [LibraryController::class, 'booksForStatus']);
            Route::post('/add', [LibraryController::class, 'userAddToLibrary']);
            Route::get('/delete/{books}', [LibraryController::class, 'userDeleteFromLibrary'])->middleware('books.id.control');
            Route::post('/{books}/status', [LibraryController::class, 'updateStatus'])->middleware('books.id.control');
            Route::post('/{book}/comment', [LibraryController::class, 'updateComment']);
            Route::post('/{book}/point', [LibraryController::class, 'updatePoint']);
        });

        Route::prefix('author')->controller(AuthorController::class)->middleware('check.roles:1|2')->group(function () {
            Route::post('/add', 'store');
            Route::post('/{author}', 'update')->middleware('author.id.control');
            Route::delete('/{author}', 'delete')->middleware('author.id.control');
            Route::post('/{author}/photo', 'uploadAuthorPicture')->middleware('author.id.control');
            Route::delete('/{author}/photo', 'deleteAuthorPicture')->middleware('author.id.control');
        });

        Route::prefix('publisher')->controller(PublisherController::class)->middleware('check.roles:1|2')->group(function () {
            Route::post('/add', 'store');
            Route::post('/{publisher}', 'update')->middleware('publisher.id.control');
            Route::delete('/{publisher}', 'delete')->middleware('publisher.id.control');
        });

        Route::prefix('category')->controller(CategoryController::class)->middleware('check.roles:1|2')->group(function () {
            Route::post('/add', 'store');
            Route::post('/{category}', 'update')->middleware('category.id.control');
            Route::delete('/{category}', 'delete')->middleware('category.id.control');
        });

        Route::prefix('books')->controller(BooksController::class)->middleware('check.roles:1|2')->group(function () {
            Route::post('/', 'store');
            Route::post('/{books}', 'update')->middleware('books.id.control');
            Route::delete('/{books}', 'delete')->middleware('books.id.control');
            Route::post('/{books}/photo', 'uploadBookPicture')->middleware('books.id.control');
            Route::delete('/{books}/photo', 'deleteBookPicture')->middleware('books.id.control');
            Route::post('/{books}/quotes', 'addQuotes')->middleware('books.id.control');
            Route::delete('/{books}/quotes', 'deleteQuotes');
            Route::post('/quotes/{books}', 'updateQuotes');
            Route::post('/quotes/{quotes}/comment', [CommentController::class, 'bookQuotesStore'])->middleware('quotes.user.id.control');

        });
    });
});


//public routes-> all users
Route::prefix('library')->group(function () {
    Route::prefix('users')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('/', 'limitedUserInfo');
            Route::get('/search/{search}', 'search');
            Route::get('/librarians', 'getLibrarians');
        });
        Route::get('/{user}/quotes', [BooksController::class,'usersQuotess'])->middleware('user.id.control');

        Route::resource('post', PostController::class)->only(['index', 'show']); //->middleware('post.id.control');
        Route::get('{user}/post', [PostController::class, 'usersPosts'])->middleware('user.id.control');
        Route::get('{user}/comment', [CommentController::class, 'userComments'])->middleware('user.id.control');


        Route::get('/post/{post}/comment', [CommentController::class, 'commentsOfThePost'])->middleware('post.id.control');
        Route::get('/quotes/{comment}/comment', [CommentController::class, 'commentsOfTheQuotes'])->middleware('quotes.id.control');

    });
    Route::prefix('library')->controller(LibraryController::class)->group(function () {
        Route::get('/{user}/get', 'getBooksById')->middleware('user.id.control');
        Route::get('/mostbook', 'mostReadBooks');
        Route::get('/mostauthor', 'mostReadAuthors');
        Route::get('/{user}/quotes', 'getQuotes')->middleware('user.id.control');
    });

    Route::prefix('books')->controller(BooksController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{search}', 'search');
        Route::get('/{books}/get', 'getBooksById')->middleware('books.id.control');
        Route::get('/{books}/quotes', 'getQuotes')->middleware('books.id.control');
        Route::get('/{books}/point', 'point')->middleware('books.id.control');
    });

    Route::prefix('author')->controller(AuthorController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/books', 'getBooks');
        Route::get('/{author}/get', 'getBooksById')->middleware('author.id.control');
        Route::get('/{author}', 'search');
        Route::get('/{author}/quotes', 'getQuotes')->middleware('author.id.control');
    });

    Route::prefix('publisher')->controller(PublisherController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/books', 'getBooks');
        Route::get('/{publisher}/get', 'getBooksById')->middleware('publisher.id.control');
        Route::get('/{publisher}', 'search');
    });

    Route::prefix('category')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/books', 'getBooks');
        Route::get('/{search}', 'search');
        Route::get('/{category}/get', 'getBooksById')->middleware('category.id.control');
    });
    
});
